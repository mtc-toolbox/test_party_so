const wsHost = "ws://localhost:8080";

const STATE_CODE_OK = 200;
const STATE_CODE_ACCESS_DENIED = 403;
const STATE_CODE_UNKNOWN = 500;

var wsClient = new WebSocket(wsHost);

/**
 *
 * @param msg
 * @returns {boolean}
 */
function checkMessageFormat(msg) {
  if (typeof msg === undefined) {
    return false;
  }

  if (typeof msg.state === undefined) {
    return false;
  }

  if (typeof msg.state.code === undefined) {
    return false;
  }

  if (typeof msg.action === undefined) {
    return false;
  }

  if (typeof msg.data === undefined) {
    return false;
  }

  return true;
}

function buildWSMessage(action, data = []) {
  let msg = {
    'token' : accessToken,
    'action': action,
    'data': data
  };

  return JSON.stringify(msg);
}

wsClient.onmessage = function (e) {
  let response = JSON.parse(e.data);
  if (checkMessageFormat(response) && response.state.code == STATE_CODE_OK) {
    switch (response.action) {
      case 'refresh':
        location.reload();
        break;
    }
  }
};

$('#generate-apples').on('click', function (e) {
  e.preventDefault();
  wsClient.send(buildWSMessage('generate'));
});
