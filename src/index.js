/**
 * External dependencies
 */
import { createRoot } from '@wordpress/element';
// import './data/store';

/**
 * Internal dependencies
 */
import App from './App';

// Import the stylesheet for the plugin.
import './style/tailwind.css';
import './style/main.scss';

// Render the App component into the DOM
const container = document.getElementById('ristopos');
const root = createRoot(container);
root.render(<App />);
