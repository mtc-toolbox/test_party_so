const wsHost = "ws://localhost:8080";

const STATE_CODE_OK = 200;
const STATE_CODE_ACCESS_DENIED = 403;
const STATE_CODE_UNKNOWN = 500;

const APPLE_STATE_FALLED = 1;
const APPLE_STATE_BAD = 3;
const APPLE_STATE_DELETED = 2;

const APPLE_TIME_TO_BAD = 5 * 3600 * 1000;

const APPLE_TEXT_CAN_EAT = 'Можно есть';
const APPLE_TEXT_BAD = 'Испортилось';

var wsClient = new WebSocket(wsHost);

var socketEvents = [];

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

/**
 *
 * @param action
 * @param data
 * @returns {string}
 */
function buildWSMessage(action, data = {}) {
  let msg = {
    'token': accessToken,
    'action': action,
    'data': data
  };

  return JSON.stringify(msg);
}

function wsRedrawApple(id) {
  wsClient.send(buildWSMessage('redraw', {'id': id}));
}

/**
 *
 * @param id
 */
function drawFalledApple(id, percent = '100.00%', message = APPLE_TEXT_CAN_EAT, ttb = APPLE_TIME_TO_BAD) {
  $('.apple-download.enabled-tool-button[data-key=' + id + ']').off('click');

  $('.apple-container[data-key=' + id + ']').attr('time-to-bad', ttb);

  if (socketEvents[ttb] === undefined) {
    socketEvents[ttb] = setInterval(wsRedrawApple, ttb, id);
  }

  $('.apple-download[data-key=' + id + ']').removeClass('enabled-tool-button');
  $('.apple-download[data-key=' + id + ']').addClass('disabled-tool-button');

  $('.apple-download[data-key=' + id + '] .fa-download').removeClass('enabled-tool-button');
  $('.apple-download[data-key=' + id + '] .fa-download').addClass('disabled-tool-button');

  $('.apple-eat[data-key=' + id + ']').removeClass('disabled-tool-button');
  $('.apple-eat[data-key=' + id + ']').addClass('enabled-tool-button');

  $('.apple-eat[data-key=' + id + '] .fa-apple').removeClass('disabled-tool-button');
  $('.apple-eat[data-key=' + id + '] .fa-apple').addClass('enabled-tool-button');

  $('.apple-container[data-key=' + id + '] .apple-eated').text(percent);

  $('.apple-container[data-key=' + id + '] .apple-state').text(message);

  $('.apple-eat.enabled-tool-button[data-key=' + id + ']').on('click', function (e) {
    e.preventDefault();
    alert('Eat 1:' + $(this).attr('data-key'));
  });
}

/**
 *
 * @param id
 */
function drawBadApple(id, message = APPLE_TEXT_BAD) {

  $('.apple-download.enabled-tool-button[data-key=' + id + ']').off('click');
  $('.apple-eat.enabled-tool-button[data-key=' + id + ']').off('click');

  $('.apple-download[data-key=' + id + ']').removeClass('enabled-tool-button');
  $('.apple-download[data-key=' + id + ']').addClass('disabled-tool-button');

  $('.apple-download[data-key=' + id + '] .fa-download').removeClass('enabled-tool-button');
  $('.apple-download[data-key=' + id + '] .fa-download').addClass('disabled-tool-button');

  $('.apple-eat[data-key=' + id + ']').removeClass('enabled-tool-button');
  $('.apple-eat[data-key=' + id + ']').addClass('disabled-tool-button');

  $('.apple-container[data-key=' + id + '] .apple-eated').remove();
  $('.apple-container[data-key=' + id + '] .apple-state').text(message);
  $('.apple-container[data-key=' + id + '] .apple-image').css('color', 'black');

}


/**
 *
 * @param id
 */
function deleteApple(id) {

  $('.apple-container[data-key=' + id + ']').remove();
}

function redrawApple(data) {
  let id = data.id;
  let eated = data.eated;
  let message = data.message;
  let timeToBad = data.ttb;
  let state = data.state;

  switch (state) {
    case APPLE_STATE_FALLED:
      drawFalledApple(id, eated, message, timeToBad);
      break;
    case APPLE_STATE_BAD:
      drawBadApple(id, message);
      break;
    case APPLE_STATE_DELETED:
      deleteApple($id);
  }
}

wsClient.onmessage = function (e) {
  let response = JSON.parse(e.data);
  if (checkMessageFormat(response) && response.state.code == STATE_CODE_OK) {
    switch (response.action) {
      case 'refresh':
        location.reload();
        break;
      case 'redraw':
        redrawApple(response.data);
        break;
    }
  }
};

$('#generate-apples').on('click', function (e) {
  e.preventDefault();
  wsClient.send(buildWSMessage('generate'));
});

$('.apple-download.enabled-tool-button').on('click', function (e) {
  e.preventDefault();
  alert('Fall:' + $(this).attr('data-key'));
  wsClient.send(
    buildWSMessage('fall',
      {'id': $(this).attr('data-key')}
    )
  );
});

$('.apple-eat.enabled-tool-button').on('click', function (e) {
  e.preventDefault();
  alert('Eat 2:' + $(this).attr('data-key'));
});

drawFalledApple(12);
drawBadApple(14);
