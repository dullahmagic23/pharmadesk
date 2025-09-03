// resources/js/utils/spinner.js
import { Spinner } from 'spin.js';

let spinner = null;

const opts = {
    lines: 13,
    length: 20,
    width: 10,
    radius: 20,
    scale: 1,
    corners: 1,
    speed: 1,
    rotate: 0,
    // animation: 'spinner-line-fade-quick',
    direction: 2,
    color: '#3b82f6',
    fadeColor: 'transparent',
    top: '50%',
    left: '50%',
    shadow: '0 0 1px transparent',
    zIndex: 2000000000,
    className: 'spinner',
    position: 'absolute',
};

export function startSpinner(targetId = 'app') {
  const target = document.getElementById(targetId) || document.body;
  spinner = new Spinner(opts).spin(target);
}

export function stopSpinner() {
  if (spinner) {
    spinner.stop();
    spinner = null;
  }
}
