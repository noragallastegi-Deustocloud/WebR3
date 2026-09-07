const chatButton = document.getElementById('chat-button');
const chatBox = document.getElementById('chat-box');
const chatClose = document.getElementById("chat-close");

const input = document.getElementById("userMessage");

// Escucha Enter
input.addEventListener("keydown", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    sendMessage();
  }
});

// Añadir mensaje al chat
function appendMessage(text, sender, id = null) {
  const messages = document.getElementById('chat-messages');
  const div = document.createElement('div');
  div.classList.add('message', sender);
  if (id) div.id = id;
  div.textContent = text;
  messages.appendChild(div);
  messages.scrollTop = messages.scrollHeight;
}

// Enviar mensaje
async function sendMessage() {
  const inputEl = document.getElementById('userMessage');
  const message = inputEl.value.trim();
  if (!message) return;

  appendMessage(message, 'user');
  inputEl.value = '';

  // Mostrar burbuja typing
  showTyping();

  try {
    const res = await fetch('ChatIA/chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message })
    });

    const raw = await res.text();
    console.log("Respuesta cruda:", raw);

    let data;
    try {
      data = JSON.parse(raw);
    } catch (err) {
      hideTyping();
      appendMessage("⚠️ Respuesta no válida desde el servidor", 'bot');
      return;
    }

    let botMessage = '';

    if (data.output && data.output.length > 0) {
      const firstBlock = data.output[0];
      if (firstBlock.content && firstBlock.content.length > 0) {
        botMessage = firstBlock.content[0].text || '';
      }
    } else if (data.error) {
      botMessage = "⚠️ " + data.error;
    }

    hideTyping();
    appendMessage(cleanBotMessage(botMessage), 'bot');

  } catch (err) {
    hideTyping();
    appendMessage("⚠️ Error al conectar con la IA.", 'bot');
    console.error(err);
  }
}

// Mostrar la burbuja "escribiendo..."
let typingBubble = null;

function showTyping() {
  const messages = document.getElementById('chat-messages');

  typingBubble = document.createElement("div");
  typingBubble.classList.add("message", "bot", "typing");

  typingBubble.innerHTML = `
    <span></span><span></span><span></span>
  `;

  messages.appendChild(typingBubble);
  messages.scrollTop = messages.scrollHeight;
}

function hideTyping() {
  if (typingBubble) {
    typingBubble.remove();
    typingBubble = null;
  }
}


// Botones de abrir/cerrar chat
chatButton.addEventListener('click', () => {
    const svgBubble = `<svg class="icon icon-bubble" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 143 143" fill="white">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M17.875 35.6487C17.875 29.1244 23.2077 23.8334 29.7917 23.8334H113.208C119.792 23.8334 125.125 29.1244 125.125 35.6487V88.815C125.12 90.3736 124.807 91.9158 124.205 93.3534C123.603 94.7911 122.723 96.0959 121.616 97.1933C120.51 98.2907 119.197 99.1591 117.755 99.7489C116.312 100.339 114.767 100.638 113.208 100.63H73.8833L56.8008 116.718C51.1106 122.074 41.7083 118.076 41.7083 110.283V100.63H29.7917C23.2077 100.63 17.875 95.3393 17.875 88.815V35.6487ZM51.8733 58.344C51.3189 57.7916 50.6611 57.3538 49.9376 57.0556C49.214 56.7574 48.4388 56.6046 47.6561 56.606C46.8735 56.6074 46.0988 56.7629 45.3763 57.0637C44.6538 57.3644 43.9976 57.8046 43.4452 58.3589C42.8928 58.9133 42.455 59.5711 42.1567 60.2946C41.8585 61.0182 41.7057 61.7934 41.7071 62.5761C41.7085 63.3587 41.864 64.1334 42.1648 64.8559C42.4656 65.5784 42.9057 66.2346 43.4601 66.787C44.0145 67.3394 44.6722 67.7772 45.3958 68.0755C46.1194 68.3737 46.8946 68.5264 47.6772 68.5251C48.4598 68.5237 49.2345 68.3682 49.957 68.0674C50.6795 67.7666 51.3357 67.3265 51.8881 66.7721C52.4406 66.2177 52.8784 65.56 53.1766 64.8364C53.4748 64.1128 53.6276 63.3376 53.6262 62.555C53.6248 61.7724 53.4693 60.9977 53.1685 60.2752C52.8678 59.5527 52.4276 58.8965 51.8733 58.344ZM67.2755 58.356C68.107 57.5207 69.1675 56.9508 70.3228 56.7183C71.4782 56.4858 72.6765 56.6012 73.7663 57.0498C74.8561 57.4984 75.7884 58.2601 76.4453 59.2386C77.1022 60.2171 77.4541 61.3684 77.4566 62.5469C77.4591 63.7254 77.112 64.8782 76.4593 65.8595C75.8066 66.8408 74.8776 67.6064 73.7897 68.0597C72.7018 68.5129 71.5039 68.6333 70.3476 68.4057C69.1913 68.1781 68.1284 67.6127 67.2934 66.781C66.174 65.6661 65.5433 64.1521 65.54 62.5722C65.5366 60.9923 66.1609 59.4757 67.2755 58.356ZM99.5399 58.344C98.4203 57.2284 96.9034 56.6032 95.3228 56.606C93.7422 56.6088 92.2275 57.2393 91.1119 58.3589C89.9962 59.4785 89.371 60.9955 89.3738 62.5761C89.3766 64.1566 90.0071 65.6713 91.1267 66.787C91.6811 67.3394 92.3389 67.7772 93.0625 68.0755C93.786 68.3737 94.5613 68.5264 95.3439 68.5251C96.1265 68.5237 96.9012 68.3682 97.6237 68.0674C98.3462 67.7666 99.0024 67.3265 99.5548 66.7721C100.107 66.2177 100.545 65.56 100.843 64.8364C101.141 64.1128 101.294 63.3376 101.293 62.555C101.291 61.7724 101.136 60.9977 100.835 60.2752C100.534 59.5527 100.094 58.8965 99.5399 58.344Z" fill="white"/>
    </svg>`;
  const svgClose = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>`;

  const isOpen = chatBox.classList.contains("open");

  if (isOpen) {
    chatBox.classList.remove("open");
    chatButton.innerHTML = svgBubble;
    chatButton.classList.remove("open");
  } else {
    chatBox.classList.add("open");
    chatButton.innerHTML = svgClose;
    chatButton.classList.add("open");

    if (!chatBox.dataset.welcomed) {
      appendMessage("¡Hola! Soy ProyectoR3, tu asistente IA. ¿En qué puedo ayudarte hoy?", "bot");
      chatBox.dataset.welcomed = "true";
    }
  }
});

chatClose.addEventListener("click", () => {
  const svgBubble = `...`;
  chatBox.classList.remove("open");
  chatButton.innerHTML = svgBubble;
  chatButton.classList.remove("open");
});

function cleanBotMessage(text) {
  return text
    .replace(/\*\*/g, "")         // quitar **
    .replace(/\[[^\]]*\]/g, "")   // quitar [cualquier cosa dentro]
    .replace(/【[^】]*】/g, "")    // quitar referencias tipo 
    .replace(/\s+/g, " ")         // limpiar espacios dobles
    .trim();
}
