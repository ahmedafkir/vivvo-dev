var image_gallery_glider;

document.observe('dom:loaded', function() {
	image_gallery_glider = new Glider('image_gallery_stripe_body', {duration:0.5});
});