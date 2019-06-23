// Web Font Loader
var WebFont = require('webfontloader');

var fontFamilies = ['Poppins:200,300', 'Roboto:300,400,500', 'Material Icons'];

WebFont.load({
 google: {
   families: fontFamilies
 }
});
