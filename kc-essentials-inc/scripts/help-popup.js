jQuery(document).ready(function(e){var d=e("body"),c=e(".contextual-help-tabs-wrap").children(),a=e(".contextual-help-sidebar"),b="",f=null;if(c.length){c.each(function(){b+='<h3 class="title">'+e.trim(e("#tab-link-"+this.id.replace("tab-panel-","")).text())+"</h3>";b+=e(this).html()})}if(a.children().length){b+="<hr />"+a.html()}if(b!==""){f=e('<div id="kc-help-popup" class="hidden"><div class="_wrap"><div class="_inside">'+b+"</div></div></div>").appendTo("body");e(document).bind("keypress",function(g){if(!e(g.target).is(":input")&&g.which==63){if(f.is(":visible")){f.trigger("close")}else{f.css({width:d.width()*0.85,height:d.height()*0.85}).lightbox_me({centered:true,destroyOnClose:true,showOverlay:true,overlaySpeed:10,lightboxSpeed:10,overlayCSS:{background:"#fff",opacity:".1"}})}g.preventDefault()}})}});