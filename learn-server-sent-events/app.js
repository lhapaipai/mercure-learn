let evtSource = new EventSource("/back/index.php");

console.log('start !');

const eventList = document.getElementById("list");

evtSource.onopen = function (event) {
  console.log("Connexion établie avec le serveur", event)
}

evtSource.onmessage = function (event) {
  const newElement = document.createElement("li");
  newElement.textContent = `message: ${event.data}`
  eventList.appendChild(newElement)
}

evtSource.onerror = function (err) {
  console.log("EventSource failed:", err);
};

evtSource.addEventListener('news', function (event) {
  const newElement = document.createElement("li");
  const data = JSON.parse(event.data);
  newElement.textContent = `message: ${data.title} (${data.content})`
  eventList.appendChild(newElement)
})



window.addEventListener('beforeunload', () => {
  evtSource.close();
})