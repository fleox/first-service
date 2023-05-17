/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

/********************/
/** COUNTDOWN ****/
/******************/
const needCountDown = document.querySelectorAll('.countdown');
if (needCountDown.length) {
    let loadCountDown = (element) => {
        let content = element || "body";

        import(/* webpackChunkName: "js/modules/countDown" */ './modules/countDown').then(countDown => {
            countDown.init();
        });
    }

    global.loadCountDown = loadCountDown;
    loadCountDown();
}
