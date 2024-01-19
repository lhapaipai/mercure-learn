# Mercure & Co

Pour un premier aperçu de Mercure et de ses possibilités.

3 dossiers 

- learn-server-sent-event : pour apprendre la logique JavaScript derrière tout cela (pas de Mercure)
- mercure-only : pour une implémentation à partir de la spec
- mercure-symfony : pour une utilisation basique sous Symfony


## Server Sent Events

sources:

- https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events
- https://fr.javascript.info/server-sent-events
- https://github.com/hhxsv5/php-sse

### Usage

```bash
./start-server.sh

# se rendre sur la page : http://127.0.0.1:9001
```

### Description

EventSource est un moyen de communication avec le serveur moins puissant que WebSocket.

Pourquoi l’utiliser ?

La raison principale : c’est plus simple. Dans de nombreuses applications, la puissance de WebSocket n'est pas nécessaire.

EventSource permet de recevoir un flux de données du serveur. Il prend également en charge la reconnexion automatique, quelque chose que nous devons implémenter manuellement avec WebSocket. Enfin il utilise le protocole HTTP, pas un nouveau protocole.

### Format des messages envoyés par le serveur

Le serveur communique via http avec l'en-tête `Content-Type: text/event-stream`.

Il doit envoyer des messages avec plusieurs champs, un par ligne

Tout autre nom de champ est ignoré. Si une ligne ne contient aucun caractère deux-points, la ligne entière est considérée comme le nom du champ, avec un contenu vide.

le message doit se terminer par 2 retours à la ligne consécutifs `\n\n`.

```yaml
# si la  ligne commence par un `:` elle est considérée comme un
# commentaire. cela peut être utile pour maintenir la connexion si
# des messages doivent être transmis de façon irrégulière
: ceci est un commentaire

# si pas de champs event l'événement sera écouté avec `onmessage`.
event: <event-name> # celui qui sera écouté avec `addEventListener`

# l'identifiant de l'événement qui sera mémorisé comme valeur
# d'identifiant du dernier évènement de l'objet EventSource
id: <your-id>

# si le serveur reçoit plusieurs lignes consécutives commençant
# par `data:` il les concatène en ajoutant un \n entre chacune
data: ligne 1
data: ligne 2

# délai de reconnexion en millisecondes à utiliser lors de la
# tentative d'envoi de l'évènement
retry: 3000
```

### exemples

```bash
event: userconnect
data: {"username": "bobby", "time": "02:33:48"}

data: Ici un message système quelconque qui sera utilisé
data: pour accomplir une tâche.

event: usermessage
data: {"username": "bobby", "time": "02:34:11", "text": "Hi everyone."}
```

### EventSource

L'interface `EventSource` est utilisée afin de recevoir des évènements envoyés par le serveur. Elle se connecte via HTTP et reçoit des évènements avant de clôturer la connexion.

```js
let evtSource = new EventSource("/back/index.php");

evtSource.onopen = function (event) {
  console.log("Connexion établie avec le serveur", event)
}

evtSource.onmessage = function (event) {
  console.log(`message: ${event.data}`)
}

evtSource.onerror = function (err) {
  console.log("EventSource failed:", err);
};

evtSource.addEventListener('news', function (event) {
  console.log(`news:`, JSON.parse(event.data))
})

window.addEventListener('beforeunload', () => {
  evtSource.close();
})
```

### Requêtes Cross-origin

EventSource prend en charge les requêtes cross-origin, nous pouvons utiliser une URL d'une autre source.

Pour transmettre les informations d’identification, nous devons définir l’option supplémentaire `withCredentials`.

```js
let source = new EventSource("https://another-site.com/events", {
  withCredentials: true
});
```

Le serveur distant obtiendra l’en-tête Origin et doit répondre avec `Access-Control-Allow-Origin` pour continuer.


## Utilisation de Mercure seul

dossier `mercure-only`


## Mercure Symfony

### Prérequis pour une utilisation en local

Soit utiliser le binaire `mercure` fournit sur le dépot github : https://github.com/dunglas/mercure/releases. C'est un caddy server avec le module mercure intégré.

Soit re-générer sa propre version de caddy server (si on veut ajouter davantage de modules)

#### xcaddy

```bash
sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/xcaddy/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-xcaddy-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/xcaddy/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-xcaddy.list
sudo apt update
sudo apt install xcaddy
```

#### caddy avec le module mercure

```bash
xcaddy build \
  --with github.com/dunglas/mercure/caddy
```

### Installation

```bash
composer install

./mercure run
# ou bien
./caddy run

# rendez-vous sur https://mercure-symfony.localhost
# mercure ui sur https://mercure-symfony.localhost/.well-known/mercure/ui/
```