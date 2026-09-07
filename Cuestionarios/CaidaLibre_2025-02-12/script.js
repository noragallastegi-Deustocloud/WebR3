// Enviar todas las respuestas al servidor
async function enviarRespuestas() {
  const payload = {
    age: Number(sessionStorage.getItem('edad')),
    r1: sessionStorage.getItem('pregunta'),
    r2: sessionStorage.getItem('r2'),
    r3: sessionStorage.getItem('r3'),
    r4: sessionStorage.getItem('r4'),
    r5: sessionStorage.getItem('r5'),
    r6: sessionStorage.getItem('r6'),
    name: sessionStorage.getItem('nombre'),
    start: sessionStorage.getItem('comienzo'),
    school: sessionStorage.getItem('colegio'),
    date: sessionStorage.getItem('fecha')
  };

  // Cambia esta URL para que apunte a tu servidor local o de producción
  const res = await fetch("http://localhost:3000/api/answers", {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  return res.ok;
}