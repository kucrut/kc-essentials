(function(e,i){var a="kcPostFinder",c=false,l=e(i),k=[".kc-find-post"],g,f,b,j,h,d=function(){return k.join(", ")};activate=function(){f=e("#find-posts-input");b=e("#find-posts-response");j=e("#find-posts-submit");h=e("#find-posts-close");j.on("click.kcPostFinder",function(q){q.preventDefault();if(!g.data("kcTarget")){return}var o=b.find("input:checked");if(!o.length){return false}var m=g.data("kcTarget"),p=m.val(),p=p===""?[]:p.split(","),n=o.val();if(m.is(".unique")){m.val(n)}else{if(e.inArray(n,p)<0){p.push(n);m.val(p.join(","))}}});l.on("dblclick.kcPostFinder",'input[name="found_post_id"]',function(){j.trigger("click.kcPostFinder")});l.on("click.kcPostFinder","#find-posts-close",function(){f.val("");g.removeData("kcTarget")});c=true},deactivate=function(){unbind();j.off("click.kcPostFinder");l.off("dblclick.kcPostFinder");l.off("click.kcPostFinder");g=f=b=j=h=null;c=false},action=function(m){g.data("kcTarget",e(this));findPosts.open()},bind=function(){l.on("dblclick.kcPostFinder",d(),action)},unbind=function(){l.off("dblclick.kcPostFinder",d(),action)},publicMethod=e[a]=function(m){var n=this;if(c){if(!m){return}unbind()}else{g=e("#find-posts");if(!g.length){return}activate()}if(m){k=k.concat(m.split(","))}bind();return n};publicMethod.destroy=function(){deactivate()}}(jQuery,document));(function(f,j){var b="kcRowCloner",c=false,l=f(j),g={add:[],del:[]},e=function(){i();c=true},a=function(){m();c=false;g={add:[],del:[]}},d=function(r){var q=f(r.target),p;if(q.is("a.add")){p=n}else{if(q.is("a.del")){p=k}else{return}}r.preventDefault();var o=f(r.currentTarget);$block=o.parent();p.call(r,{anchor:q,item:o,block:$block})},n=function(o){console.log(o)},k=function(o){var p=this;o.isLast=!o.item.next("li.row").length;o.removed=true;o.item.slideUp(function(){if(!o.item.siblings(".row").length){o.item.find('input[type="text"]').val("");o.item.find('input[type="checkbox"]').prop("checked",false);o.item.find(".hasdep").trigger("change");o.removed=false}else{o.item.remove()}for(var q=0;q<g.del.length;q++){g.del[q].call(p,o)}})},i=function(){l.on("click.kcRowCloner","li.row",d)},m=function(){l.off("click.kcRowCloner","li.row",d)},h=f[b]=function(){var o=this;if(c){return}e();return o};h.destroy=function(){a()};h.addCallback=function(o,p){if(g.hasOwnProperty(o)&&f.isFunction(p)){g[o].push(p)}}})(jQuery,document);(function(b){var a=b(document);b(".widgets-sortables .hasdep").kcFormDep();b(".widgets-sortables").ajaxSuccess(function(){b(".hasdep",this).kcFormDep()});a.on("click",".kcw-control-block a.add",function(f){f.preventDefault();var d=b(this),c=d.parent().prev(".row");if(c.is(":hidden")){c.slideDown()}else{$nu=c.clone(true).hide();c.after($nu);$nu.slideDown().kcReorder(d.attr("rel"),false).find(".hasdep").kcFormDep()}});b.kcRowCloner();b.kcRowCloner.addCallback("del",function(c){if(c.removed&&!c.isLast){c.block.kcReorder(c.anchor.attr("rel"),true)}});b.kcPostFinder()})(jQuery);