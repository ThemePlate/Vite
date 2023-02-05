import text from './shared';
import './main.css';

console.log( 'ThemePlate!' );
console.log( `Shared: ${text}` );

import('./views/foo').then((value) => console.log(`Foo ${value.default}`));
