import { Controller } from '@hotwired/stimulus';
import * as bootstrap from "bootstrap";

export default class extends Controller {
  open() {
    const interval = setInterval(() => {
      const modal = document.getElementById('main-modal');

      if (!modal.hasAttribute('complete')) {
        return;
      }

      const myModal = new bootstrap.Modal(modal, {
        keyboard: false
      });

      myModal.show();

      clearInterval(interval);
    }, 20);
  }
}
