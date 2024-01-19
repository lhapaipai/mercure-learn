

const host = 'https://localhost';

const token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiKiJdfSwiaWF0IjoxNzA1NTUwNjcxfQ.zccZIIvy3MH0h7dRKWUgUPLw6-wHh5O3JB5sZjMV1o0';

const url = new URL(`${host}/.well-known/mercure`);
// système de template qui permet d'écouter plusieurs URL
// url.searchParams.append('topic', 'https://example.com/books/{id}');

// permet d'écouter uniquement ce topic ci
// url.searchParams.append('topic', 'http://centaure/hello');
// url.searchParams.append('topic', 'https://example.com/my-private-topic');
url.searchParams.append('topic', '/.well-known/mercure/subscriptions/{/topic}/{/subscriber}');

const eventSource = new EventSource(url);

const $output = document.getElementById('output');
eventSource.onmessage = e => {
  console.log(e)
  let newContent = document.createElement('div');
  newContent.innerHTML = `${e.data} (${e.lastEventId})`;
  $output?.append(newContent)
}


window.addEventListener('beforeunload', () => {
  eventSource.close();
})

document.getElementById('reset')?.addEventListener('click', (e) => {
  e.preventDefault();
  eventSource.close();
})

document.getElementById('mercure-form')?.addEventListener('submit', (e) => {
  e.preventDefault();
  console.log(e, e.target)
  fetch('https://localhost/.well-known/mercure', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      Authorization: `Bearer ${token}`,
    },
    body: new URLSearchParams(new FormData(e.target as HTMLFormElement))
  })
    .then(res => res.text())
    .then(data => {
      console.log('send', data)

      let newContent = document.createElement('div');
      newContent.innerHTML = `Send data (${data})`;
      $output?.append(newContent)
    })
})