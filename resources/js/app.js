import './bootstrap';
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import intersect from '@alpinejs/intersect';

Alpine.plugin(persist);
Alpine.plugin(intersect);
window.Alpine = Alpine;

Alpine.start();
