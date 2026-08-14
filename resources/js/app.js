/**
 * Load Bootstrap (default Laravel)
 */
import './bootstrap';

/**
 * Load Alpine.js (WAJIB untuk dropdown Laravel)
 */
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * Load custom script login
 */
import './login';

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

window.TomSelect = TomSelect;