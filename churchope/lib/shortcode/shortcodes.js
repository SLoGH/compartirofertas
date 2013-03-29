(function() {
    tinymce.create('tinymce.plugins.th_buttons', {
		 
        init : function(ed, url){
            ed.addButton('highlight', {
            title : 'Highlight',
                onclick : function() {
					
                    ed.focus();
					ed.selection.setContent(' [highlight] ' + ed.selection.getContent() + ' [/highlight] ');
                   
                },
             image:  url +  "/shortcodes/img/ed_highlight.png"
            });
			
			
			ed.addButton('dropcaps', {
            title : 'Dropcaps',
                onclick : function() {
					
                    ed.focus();
					ed.selection.setContent(' [dropcaps] 1 [/dropcaps] ');
                   
                },
             image:  url +  "/shortcodes/img/ed_dropcaps.png"
            });
			
			ed.addButton('totop', {
            title : 'To Top',
                onclick : function() {
					
                    ed.focus();
					ed.selection.setContent(' [totop] to Top [/totop] ');
                   
                },
             image:  url +  "/shortcodes/img/ed_totop.png"
            });
			
			ed.addCommand('buttons', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/buttons.php'+th_wpml_lang,
					width : 350,
					height : 460,
					inline : 1
				});
			
			});
						
			ed.addButton('buttons', {
            title : 'Insert Button',
               cmd : 'buttons',
               image:  url +  "/shortcodes/img/ed_buttons.png"
            });		
			
			ed.addCommand('toc', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/toc.php'+th_wpml_lang,
					width : 350,
					height : 250,
					inline : 1
				});
			
			});
						
			ed.addButton('toc', {
            title : 'Insert Table of Contents',
               cmd : 'toc',
               image:  url +  "/shortcodes/img/ed_toc.png"
            });	
			
			
			ed.addCommand('event', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/event.php'+th_wpml_lang,
					width : 350,
					height : 470,
					inline : 1
				});
			
			});
			ed.addButton('event', {
            title : 'Insert Events Calendar',
               cmd : 'event',
               image:  url +  "/shortcodes/img/ed_event.png"
            });
			
			ed.addCommand('blog', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/blog.php'+th_wpml_lang,
					width : 350,
					height : 410,
					inline : 1
				});
			});
			ed.addButton('blog', {
            title : 'Insert Blog',
               cmd : 'blog',
               image:  url +  "/shortcodes/img/ed_blog.png"
            });	
			
			ed.addCommand('contactForm', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/contactForm.php'+th_wpml_lang,
					width : 900,
					height : 700,
					inline : 1
				});
			});
			ed.addButton('contactForm', {
            title : 'Insert Contact Form',
               cmd : 'contactForm',
               image:  url +  "/shortcodes/img/ed_contactForm.png"
            });
			
			ed.addCommand('terms_gallery', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/gallery.php'+th_wpml_lang,
					width : 350,
					height : 500,
					inline : 1
				});
			});
			ed.addButton('terms_gallery', {
            title : 'Insert Gallery',
               cmd : 'terms_gallery',
               image:  url +  "/shortcodes/img/ed_gallery.png"
            });
			
			ed.addCommand('notifications', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/notifications.php'+th_wpml_lang,
					width : 350,
					height : 330,
					inline : 1
				});
			
			});
						
			ed.addButton('notifications', {
            title : 'Insert Notification',
               cmd : 'notifications',
               image:  url +  "/shortcodes/img/ed_notifications.png"
            });	
			
			ed.addButton('divider', {
            title : 'Insert Separator line',
              image:  url +  "/shortcodes/img/ed_divider.png",
			  onclick : function() {
                ed.selection.setContent("<hr>");
            }
            });		
			
			
			ed.addCommand('toggle', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/toggle.php'+th_wpml_lang,
					width : 350,
					height : 300,
					inline : 1
				});
			
			});
						
			ed.addButton('toggle', {
            title : 'Insert Toggle',
               cmd : 'toggle',
               image:  url +  "/shortcodes/img/ed_toggle.png"
            });		
			ed.addButton('tabs', {
            title : 'Insert Tabs',
                  onclick : function() {
                    ed.focus();
					ed.selection.setContent('[tabgroup] <br>[tab title="Tab 1"]'+ ed.selection.getContent() +'[/tab] <br>[tab title="Tab 2"]Tab 2 content goes here.[/tab] <br>[tab title="Tab 3"]Tab 3 content goes here.[/tab] <br>[/tabgroup]');
                   
                },
               image:  url +  "/shortcodes/img/ed_tabs.png"
            });		
			
			ed.addCommand('price_table_group', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/price_table.php'+th_wpml_lang,
					width : 350,
					height : 550,
					inline : 1
				});
			
			});
			ed.addButton('price_table_group', {
            title : 'Insert Price Table',
               cmd : 'price_table_group',
               image:  url +  "/shortcodes/img/ed_price_table.png"
            });
			
			ed.addCommand('social_link', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/social_link.php'+th_wpml_lang,
					width : 350,
					height : 470,
					inline : 1
				});
			
			});
			ed.addButton('social_link', {
            title : 'Insert Social Link',
               cmd : 'social_link',
               image:  url +  "/shortcodes/img/ed_social.png"
            });
			
			ed.addCommand('social_button', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/social_button.php'+th_wpml_lang,
					width : 350,
					height : 700,
					inline : 1
				});
			
			});
			ed.addButton('social_button', {
            title : 'Insert Share Button',
               cmd : 'social_button',
               image:  url +  "/shortcodes/img/ed_social_button.png"
            });	
			ed.addCommand('teaser', function() {
				ed.windowManager.open({
					file : url +  '/shortcodes/teaser.php'+th_wpml_lang,
					width : 350,
					height : 550,
					inline : 1
				});
			
			});
			ed.addButton('teaser', {
            title : 'Insert Teaser',
               cmd : 'teaser',
               image:  url +  "/shortcodes/img/ed_teaser.png"
            });	

			ed.addCommand('upcoming', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/upcoming.php'+th_wpml_lang,
					width : 350,
					height : 470,					
					inline : 1				
				});
			
			});
			ed.addButton('upcoming', {
            title : 'Upcoming events',
               cmd : 'upcoming',
               image:  url +  "/shortcodes/img/ed_upcoming.png"
            });	
			ed.addCommand('testimonials', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/testimonials.php'+th_wpml_lang,

					width : 350,
					height : 400,					
					inline : 1				
				});
			
			});
			ed.addButton('testimonials', {
            title : 'Testimonials',
               cmd : 'testimonials',
               image:  url +  "/shortcodes/img/ed_testimonials.png"
            });	
			ed.addCommand('thvideo', function() {
				ed.windowManager.open({				
					file : url +  '/shortcodes/video.php'+th_wpml_lang,

					width : 350,
					height : 270,					
					inline : 1				
				});
			
			});
			ed.addButton('thvideo', {
            title : 'Video',
               cmd : 'thvideo',
               image:  url +  "/shortcodes/img/ed_video.png"
            });
			
		
			
        },
		createControl:function(d,e,url)
				{
				
					if(d=="columns"){
					
						d=e.createMenuButton( "columns",{
							title:"Insert Columns Shortcode",							
							icons:false							
							});
							
							var a=this;d.onRenderMenu.add(function(c,b){
								
								
								a.addImmediate(b,"Column 1/2", ' [one_half]  [/one_half] ');
								a.addImmediate(b,"Column 1/2 last", ' [one_half last=last]  [/one_half] ');
								a.addImmediate(b,"Column 1/3", ' [one_third]  [/one_third] ');
								a.addImmediate(b,"Column 1/3 last", ' [one_third last=last]  [/one_third] ');
								a.addImmediate(b,"Column 1/4", ' [one_fourth]  [/one_fourth] ');
								a.addImmediate(b,"Column 1/4 last", ' [one_fourth last=last]  [/one_fourth] ');
								a.addImmediate(b,"Column 2/3", ' [two_third]  [/two_third] ');
								a.addImmediate(b,"Column 2/3 last", ' [two_third last=last]  [/two_third] ');
								a.addImmediate(b,"Column 3/4", ' [three_fourth]  [/three_fourth] ');
								a.addImmediate(b,"Column 3/4 last", ' [three_fourth last=last]  [/three_fourth] ');								
								
								b.addSeparator();
								
								a.addImmediate(b,"Clear", '[clear]');
								
								b.addSeparator();
								
								a.addImmediate(b,"Raw", ' [raw]  [/raw] ');
							});
						return d
					
					} // End IF Statement
					
					return null
				},
		
				addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand( "mceInsertContent",false,a)}})}
    });

    tinymce.PluginManager.add('th_buttons', tinymce.plugins.th_buttons);
})();