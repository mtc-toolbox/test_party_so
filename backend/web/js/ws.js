const wsHost = "ws://localhost:8080";

const STATE_CODE_OK = 200;
const STATE_CODE_ACCESS_DENIED = 403;
const STATE_CODE_UNKNOWN = 500;

const APPLE_STATE_FALLED = 1;
const APPLE_STATE_BAD = 3;
const APPLE_STATE_DELETED = 2;

const APPLE_TIME_TO_BAD = 3600 * 5;

const APPLE_TEXT_CAN_EAT = 'Можно есть';
const APPLE_TEXT_BAD = 'Испортилось';

const WS_COMMAND_REDRAW = 'redraw';
const WS_COMMAND_REFRESH = 'refresh';
const WS_COMMAND_EAT = 'eat';

var wsClient = new WebSocket(wsHost);

initConnection();

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
  wsClient.send(buildWSMessage(WS_COMMAND_REDRAW, {'id': id}));
}

function wsEatApple(id, percent) {
  wsClient.send(buildWSMessage(WS_COMMAND_EAT, {'id': id, 'percent': percent}));
}


function showModal(id) {
  $('#currency-modal').modal('show');
  $('#eat-id').val(id);
}

/**
 *
 * @param id
 * @param percent
 * @param message
 * @param ttb
 */
function drawFalledApple(id, percent = '100.00%', message = APPLE_TEXT_CAN_EAT, ttb = APPLE_TIME_TO_BAD) {
  $('.apple-download.enabled-tool-button[data-key=' + id + ']').off('click');
  $('.apple-container[data-key=' + id + ']').attr('state', APPLE_STATE_FALLED);

  $('.apple-container[data-key=' + id + ']').attr('time-to-bad', ttb);

  if (socketEvents[id] !== undefined) {
    socketEvents.splice(id, 1);
  }

  socketEvents[id] = setTimeout(wsRedrawApple, ttb * 1000, id);

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
    showModal($(this).attr('data-key'));
  });
}

/**
 *
 * @param id
 * @param message
 */
function drawBadApple(id, message = APPLE_TEXT_BAD) {

  if (typeof (socketEvents[id]) !== undefined) {
    socketEvents.splice(id, 1);
  }

  $('.apple-download.enabled-tool-button[data-key=' + id + ']').off('click');
  $('.apple-eat.enabled-tool-button[data-key=' + id + ']').off('click');
  $('.apple-container[data-key=' + id + ']').attr('state', APPLE_STATE_BAD);

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

  $('.apple-container[data-key=' + id + ']').parent().remove();
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
      deleteApple(id);
  }
}

/**
 *
 */
function initConnection() {
  wsClient.onmessage = function (e) {
    let response = JSON.parse(e.data);
    if (checkMessageFormat(response) && response.state.code == STATE_CODE_OK) {
      switch (response.action) {
        case WS_COMMAND_REFRESH:
          location.reload();
          break;
        case WS_COMMAND_REDRAW:
          redrawApple(response.data);
          break;
      }
    }
  };

  wsClient.onclose = function () {
    wsClient = new WebSocket(wsHost);
    initConnection();
  }
}


$('#generate-apples').on('click', function (e) {
  e.preventDefault();
  wsClient.send(buildWSMessage('generate'));
});

$('.apple-download.enabled-tool-button').on('click', function (e) {
  e.preventDefault();
  wsClient.send(
    buildWSMessage('fall',
      {'id': $(this).attr('data-key')}
    )
  );
});

$('.apple-eat.enabled-tool-button').on('click', function (e) {
  e.preventDefault();
  showModal($(this).attr('data-key'));
});

$('#eat-button').on('click', function (e) {
  e.preventDefault();
  let id = $('#eat-id').val();
  let percent = $('#eat-percent').val();
  $('#eat-form').data('yiiActiveForm').submitting = false;
  $('#eat-form').yiiActiveForm('validate');
  if (!$('#eat-form').find('.has-error').length) {
    $('#currency-modal').modal('hide');
    wsEatApple(id, percent);
  }

});
