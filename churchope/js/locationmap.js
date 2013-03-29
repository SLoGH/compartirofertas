function LocationMap(h,d){
	var w=h.mapid,l=parseInt(h.zoom,10),g=h.center,s=h.mapTypeId,x=h.title,Z=h.metaKey,a=h.pois?h.pois:[],y=h.width,v=h.height,o=false,b=d.mapName,Y=d.country,E=d.language,j=d.directionsServer,t=d.editable,n=d.directions,S=d.mapTypeControl,O=d.streetViewControl,U=d.scrollwheel,N=d.keyboardShortcuts,H=d.navigationControlOptions,ZC =d.zoomControl,Q=d.initialOpenInfo,J=d.initialOpenDirections,cb=d.postid,bb=d.traffic,R=d.initialTraffic,W=d.tooltips,M=d.overviewMapControl,G=d.overviewMapControlOptions,hb=d.user,db=d.userInitial,eb=d.userCenter,fb=d.userTitle,gb=d.userBody,X=d.control,p=null,i=null,u=null,f=null,V="",c=null,m=null,ab=d.poiList,e=this;
	this.display=function(a){
		if(t)google.load("maps","3",{
			other_params:"sensor=false",
			callback:function(){
				F(a)
				}
			});
	else{
		google.load("maps","3",{
			other_params:"sensor=false&language="+E
			});
		google.setOnLoadCallback(function(){
			F(a)
			})
		}
	};

function F(h){
	var i=document.getElementById(b);
	if(!i)return;
	var o={
		zoom:l?l:0,
		center:g?new google.maps.LatLng(parseFloat(g.lat),parseFloat(g.lng)):new google.maps.LatLng(0,0),
		mapTypeId:s,
		mapTypeControl:S,
		mapTypeControlOptions:{
			style:google.maps.MapTypeControlStyle.DEFAULT
			},
		zoomControl:ZC, // hide Zoom In Zoom out scroll
		scrollwheel:U,
		navigationControlOptions:{
			style:H.style
			},
		streetViewControl:O,
		keyboardShortcuts:N,
		overviewMapControl:M,
		overviewMapControlOptions:G
	};
	
	c=new google.maps.Map(i,o);
	if(X){
		var m = '';
		c.controls[google.maps.ControlPosition.BOTTOM].push(m)
		}
		bb&&P();
	if(typeof j=="undefined"||j.indexOf("google")==-1)j="http://maps.google.com";
	else if(j.toLowerCase().indexOf("http")==-1)j="http://"+j;
	f=new google.maps.InfoWindow;
	for(var d=0;d<a.length;d++)D(d);
	if(!g||g.lat==0&&g.lng==0)if(!l||l==0)e.recenter(null,true);else e.recenter(null,false);
	(t||ab)&&k();
	n=="inline"&&K();
	Q==true&&a[0]&&google.maps.event.addListenerOnce(c,"tilesloaded",function(){
		google.maps.event.trigger(a[0].marker,"click")
		});
	if(J==true&&a[0]){
		e.recenter(0,false);
		e.openDirections(0)
		}
		h&&h()
	}
	this.getWidth=function(){
	return y
	};
	//Post id is some as post id 
	this.getPostId = function(){
		return w;
	}
	
this.getHeight=function(){
	return v
	};
	
this.getTitle=function(){
	return x
	};
	
this.getMapid=function(){
	return w
	};
	
this.setTitle=function(a){
	x=a
	};
	
this.getMap=function(){
	return c
	};
	
this.openMarker=function(e){
	var d="<div class='location-map-overlay'>";
	d+="<div class='location-map-overlay-title'>";
	if(a[e].url)d+="<a href='"+a[e].url+"' alt='"+a[e].title+"'>"+a[e].title+"</a>";else d+=a[e].title;
	d+="</div>";
	d+="<div class='location-map-overlay-body'>"+a[e].body+"</div>";
	d+="<div class='location-map-overlay-links'>";
	if(o)d+="as<a href='#' id='"+b+"_editmarker' alt='"+mapl10n.edit+"'>"+mapl10n.edit+"</a> | <a href='#' id='"+b+"_deletemarker' alt = '"+mapl10n.del+"'>"+mapl10n.del+"</a> | <a href='#' id='"+b+"_zoommarker' alt = '"+mapl10n.zoom+"'>"+mapl10n.zoom+"</a>";
	else if(n=="inline"||n=="google")d+="<a href='#location_map_0_directions' id='"+b+"_directionslink'>"+mapl10n.directions+"</a>";
	d+="</div>";
	d+="</div>";
	A(e);
	f.setContent(d);
	f.open(c,a[e].marker);
	google.maps.event.addListenerOnce(f,"domready",function(){
		L(e)
		})
	};
	
this.openDirections=function(c){
	var d;
	d=a[c].correctedAddress?a[c].correctedAddress:a[c].title+" @"+a[c].point.lat+","+a[c].point.lng;
	switch(n){
		case"google":
			var e=j+"?daddr="+d+"&pw=3";
			window.open(e);
			break;
		case"inline":						
			mQuery("#"+b+"_directions").show();
			mQuery("#"+b+"_saddr").val("");
			mQuery("#"+b+"_daddr").val(d)
			mQuery('html, body').stop().animate({
					'scrollTop': mQuery("#"+b+"_directions").offset().top
				}, 400, 'swing');			
			}
		};

this.closeDirections=function(){
	mQuery("#"+b+"_directions").hide();
	if(i){
		i.setPanel(null);
		if(i.getMap()){
			i.setMap(null);
			for(var d=0;d<a.length;d++)a[d].marker.setMap(c)
				}
			}
};

this.geoCode=function(a,b,c){
	mQuery(a).removeClass("location-map-address-error");
	mQuery(b).html("");
	if(mQuery(a).val()==""){
		mQuery(a).addClass("location-map-address-error");
		mQuery(b).html(mapl10n.enter_address);
		return false
		}
		if(LocationMap.stringToLatLng(mQuery(a).val())){
		c();
		return true
		}
		if(!u)u=new google.maps.Geocoder;
	u.geocode({
		address:a.val(),
		region:Y,
		language:E
	},function(d,f){
		for(var e=0;e<d.length;e++)d[e].formatted_address==""&&d.splice(e,1);
		if(!d||d.length==0||f!=google.maps.GeocoderStatus.OK){
			mQuery(a).addClass("location-map-address-error");
			mQuery(b).html(mapl10n.no_address);
			return false
			}
			mQuery(a).removeClass("location-map-address-error");
		mQuery(a).val(d[0].formatted_address);
		mQuery(b).html("");
		c(d);
		return true
		})
	};
	
function P(){
	var d=R?" checked='checked' ":"",e="<div class='gmnoprint location-map-traffic-button'><div class='location-map-traffic-button-inner'><input type='checkbox' id='"+b+"_traffic_checkbox' "+d+" /> "+mapl10n.traffic+"</div></div>",a=mQuery(e).get(0);
	c.controls[google.maps.ControlPosition.TOP_CENTER].push(a);
	google.maps.event.addDomListener(a,"click",function(){
		if(!m)m=new google.maps.TrafficLayer;
		if(m.getMap()){
			mQuery("#"+b+"_traffic_checkbox").attr("checked","");
			m.setMap(null)
			}else{
			mQuery("#"+b+"_traffic_checkbox").attr("checked","checked");
			m.setMap(c)
			}
		})
}
function D(b){
	var e=getIconMarker(a[b].iconid).icon,d=getIconMarker(a[b].iconid).shadow;
	a[b].marker=new google.maps.Marker({
		position:new google.maps.LatLng(a[b].point.lat,a[b].point.lng),
		draggable:o,
		clickable:true,
		map:c,
		icon:e,
		shadow:d,
		zIndex:0
	});
	A(b);
	z(b);
	q(b)
	}
	function A(e){
	for(var c=[],b=0;b<a.length;b++){
		if(b==e)continue;
		a[b].marker&&c.push({
			marker:a[b].marker,
			zindex:a[b].marker.getZIndex()
			})
		}
		c.sort(function(a,b){
		return a.zindex-b.zindex
		});
	c.push({
		marker:a[e].marker,
		zindex:a[e].marker.getZIndex()
		});
	for(var d=0;d<c.length;d++)c[d].marker.setZIndex(d)
		}
		function z(b){
	if(o){
		a[b].marker.setTitle(mapl10n.click_and_drag);
		return
	}
	if(W)a[b].marker.setTitle(mQuery("<div>").html(a[b].title).text());else a[b].marker.setTitle(null)
		}
		function q(b){
	var c=a[b].marker;
	google.maps.event.clearListeners(c,"click");
	google.maps.event.addListener(c,"click",function(){
		e.openMarker(b)
		});
	google.maps.event.addListener(c,"dragstart",function(){
		f.close()
		});
	google.maps.event.addListener(c,"dragend",function(){
		a[b].viewport=null;
		a[b].correctedAddress=null;
		e.openMarker(b)
		})
	}
	function L(d){
	mQuery("#"+b+"_editmarker").click(function(){
		C(d);
		return false
		});
	mQuery("#"+b+"_deletemarker").click(function(){
		T(d);
		return false
		});
	mQuery("#"+b+"_zoommarker").click(function(){
		c.setCenter(a[d].marker.getPosition());
		var b=c.getZoom();
		b=parseInt(b+b*.3,10);
		if(b>19)b=19;
		c.setZoom(b);
		return false
		});
	// Open derection Link !!
	mQuery("#"+b+"_directionslink").click(function(){
		e.openDirections(d);
		return false
		})
	}
	function K(){
	mQuery("#"+b+"_get_directions").click(function(){
		var e=mQuery("#"+b+"_saddr"),d=mQuery("#"+b+"_daddr"),c=mQuery("#"+b+"_saddr_corrected"),a=mQuery("#"+b+"_daddr_corrected");
		f.close();
		B(e,d,c,a);
		return false
		});
	mQuery("#"+b+"_addrswap").click(function(){
		var c=mQuery("#"+b+"_saddr"),a=mQuery("#"+b+"_daddr"),d=c.val();
		c.val(a.val());
		a.val(d);
		mQuery("#"+b+"_get_directions").click();
		return false
		});
	mQuery("#"+b+"_print_directions").click(function(){
		var c=mQuery("#"+b+"_saddr"),a=mQuery("#"+b+"_daddr"),e=mQuery("#"+b+"_saddr_corrected"),d=mQuery("#"+b+"_daddr_corrected"),f=j+"?saddr="+c.val()+"&daddr="+a.val()+"&pw=2";
		window.open(f);
		B(c,a,e,d)
		});
	mQuery("#"+b+"_close_directions").click(function(){
		e.closeDirections();
		return false
		});
	mQuery("#"+b+"_directions .location-map-travelmode").click(function(){
		mQuery(".location-map-travelmode").removeClass("selected");
		mQuery(this).addClass("selected");
		mQuery("#"+b+"_get_directions").click()
		})
	}
	function B(g,f,l,k,j){
	var d,h=mQuery("#"+b+"_directions .location-map-travelmode.selected").attr("id");
	if(h.indexOf("walk")>=0)d=google.maps.DirectionsTravelMode.WALKING;
	else if(h.indexOf("bike")>=0)d=google.maps.DirectionsTravelMode.BICYCLING;else d=google.maps.DirectionsTravelMode.DRIVING;
	e.geoCode(g,l,function(){
		e.geoCode(f,k,function(){
			var l=document.getElementById(b+"_directions_renderer");
			if(!p)p=new google.maps.DirectionsService;
			var e={
				travelMode:d,
				provideRouteAlternatives:true
			},k=LocationMap.stringToLatLng(g.val()),h=LocationMap.stringToLatLng(f.val());
			e.origin=k?k.latLng:g.val();
			e.destination=h?h.latLng:f.val();
			p.route(e,function(d,e){
				switch(e){
					case google.maps.DirectionsStatus.OK:
						for(var b=0;b<a.length;b++)a[b].marker.setMap(null);
						if(!i)i=new google.maps.DirectionsRenderer({
						map:c,
						panel:l,
						hideRouteList:false,
						directions:d,
						draggable:true
					});
					else{
						i.setMap(c);
						i.setPanel(l);
						i.setDirections(d)
						}
						j&&j();
						break;
					case google.maps.DirectionsStatus.NOT_FOUND:
						alert(mapl10n.dir_not_found);
						break;
					case google.maps.DirectionsStatus.ZERO_RESULTS:
						alert(mapl10n.dir_zero_results);
						break;
					default:
						alert(mapl10n.dir_default+e)
						}
					})
		})
	})
}
this.addPOI=function(c){
	a.push(c);
	var b=a.length-1;
	D(b);
	k();
	e.recenter(b,true);
	this.openMarker(b);
	return b
	};
	
this.setEditingMode=function(c){
	f&&f.close();
	o=c;
	for(var b=0;b<a.length;b++){
		a[b].marker.setDraggable(o);
		z(b)
		}
	};
	
this.resize=function(){
	g.lat=c.getCenter().lat();
	g.lng=c.getCenter().lng();
	google.maps.event.trigger(c,"resize");
	c.setCenter(new google.maps.LatLng(parseFloat(g.lat),parseFloat(g.lng)))
	};
	
this.recenter=function(b,e){
	var d=new google.maps.LatLngBounds;
	if(typeof b=="undefined")b=null;
	if(a.length==0){
		c.setCenter(new google.maps.LatLng(0,0));
		!l&&c.setZoom(1);
		return
	}
	if(a.length==1)b=0;
	if(b!==null){
		if(e&&a[b].viewport&&a[b].viewport!={
			sw:{
				lat:0,
				lng:0
			},
			ne:{
				lat:0,
				lng:0
			}
		})c.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(a[b].viewport.sw.lat,a[b].viewport.sw.lng),new google.maps.LatLng(a[b].viewport.ne.lat,a[b].viewport.ne.lng)));
	else{
		c.setCenter(a[b].marker.getPosition());
		e&&c.setZoom(14)
		}
		return
}
for(var f=0;f<a.length;f++)d.extend(a[f].marker.getPosition());
if(e)c.fitBounds(d);else c.setCenter(d.getCenter())
	};
	
function T(c){
	var d=confirm(mapl10n.delete_prompt);
	if(!d)return;
	f.close();
	a[c].marker.setMap(null);
	a.splice(c,1);
	k();
	for(var b=0;b<a.length;b++)q(b)
		}
		function C(b){
	var d=a[b].title.replace(/\'/g,"&rsquo;"),e="<div id='location_map_edit_overlay'><input type='hidden' id='map_i' value='"+b+"' /><input id='location_map_edit_overlay_title' type='text' value='"+d+"' /><span>"+getIconHtml(a[b].iconid)+"</span><br/><textarea id='location_map_edit_overlay_body' cols='40'>"+a[b].body+"</textarea><div><input class='button-primary' type='button' id='location_map_edit_savemarker' value='"+mapl10n.save+"' /><input type='button' id='location_map_edit_cancelmarker' value='"+mapl10n.cancel+"' /></div></div>";
	f.setContent(e);
	f.open(c,a[b].marker);
	google.maps.event.addListenerOnce(f,"domready",function(){
		I(b)
		})
	}
	function I(b){
	mQuery("#location_map_edit_savemarker").click(function(){
		r(b);
		e.openMarker(b);
		q(b);
		k();
		return false
		});
	mQuery("#location_map_edit_cancelmarker").click(function(){
		e.openMarker(b);
		return false
		})
	}
	function r(b){
	a[b].title=mQuery("#location_map_edit_overlay_title").val();
	a[b].body=mQuery("#location_map_edit_overlay_body").val();
	k()
	}
	this.ajaxMapSave=function(h,i){
	var d;
	y=document.getElementById(b).style.width.replace("px","");
	v=document.getElementById(b).style.height.replace("px","");
	l=c.getZoom();
	g.lat=c.getCenter().lat();
	g.lng=c.getCenter().lng();
	s=c.getMapTypeId();
	mQuery("#map_i").length>0&&r(mQuery("#map_i").val());
	for(d=0;d<a.length;d++)a[d].point={
		lat:a[d].marker.getPosition().lat(),
		lng:a[d].marker.getPosition().lng()
		};
		
	var e={
		mapid:w,
		width:y,
		height:v,
		zoom:l,
		center:g,
		title:x,
		metaKey:Z,
		mapTypeId:s
	};
	
	e.pois=[];
	for(d=0;d<a.length;d++)e.pois[d]={
		point:a[d].point,
		title:a[d].title,
		body:a[d].body,
		address:a[d].address,
		correctedAddress:a[d].correctedAddress,
		iconid:a[d].iconid,
		viewport:a[d].viewport
		};
		
	var f;
	if(typeof Prototype!=="undefined"&&typeof Object.toJSON!=="undefined")f=Object.toJSON(e);else f=JSON.stringify(e);
	var j={
		action:"eventmap_map_save",
		map:f,
		postid:h
	};
	
	LocationMap.ajax("POST",j,function(a){
		if(a.status=="OK"&&a.data){
			w=a.data;
			cb=h;
			i()
			}
		})
};

function k(){
	for(var h,c="",f=0;f<a.length;f++){
		if(t)h="<td class='location-map-marker'>[icon]</td><td><b>[title]</b>[bodytext]</td></tr>";else h=a[f].poiListTemplate?a[f].poiListTemplate:d.poiListTemplate;
		h=h.toLowerCase();
		c+="<tr data-marker='"+f+"'>"+h+"</tr>";
		var i=LocationMap.parseAddress(a[f].correctedAddress),g={
			icon:getIconHtml(a[f].iconid),
			body:a[f].body?a[f].body+"<br/>":"",
			bodyText:a[f].body?mQuery("<div>"+a[f].body+"</div>").text()+"<br/>":"",
			directions:n!="none"?"<a href='#' class='poi_list_directions'>"+mapl10n.directions+"</a>":"",
			address:a[f].address?a[f].address:"",
			correctedAddress:a[f].correctedAddress?a[f].correctedAddress:"",
			parsedAddress1:i.firstLine,
			parsedAddress2:i.secondLine
			};
			
		if(a[f].title)if(a[f].url)g.title="<a href='"+a[f].url+"' class='poi_list_title'>"+a[f].title+"</a><br/>";else g.title=a[f].title+"<br/>";else g.title="";
		c=c.replace("[icon]",g.icon);
		c=c.replace("[title]",g.title);
		c=c.replace("[body]",g.body);
		c=c.replace("[bodytext]",g.bodyText);
		c=c.replace("[directions]",g.directions);
		c=c.replace("[address]",g.address);
		c=c.replace("[correctedaddress]",g.correctedAddress);
		c=c.replace("[address1]",g.parsedAddress1);
		c=c.replace("[address2]",g.parsedAddress2)
		}
		var j="<table>"+c+"</table>";
	mQuery("#"+b+"_poi_list").html(j);
	mQuery("#"+b+"_poi_list tr .poi_list_title").click(function(a){
		a.stopPropagation()
		});
	mQuery("#"+b+"_poi_list tr").click(function(){
		mQuery("#"+b+"_poi_list tr").removeClass("location-map-selected");
		var a=mQuery(this).attr("data-marker");
		if(a){
			mQuery(this).addClass("location-map-selected");
			e.openMarker(a)
			}
		});
mQuery("#"+b+"_poi_list tr .poi_list_directions").click(function(){
	mQuery("#"+b+"_poi_list tr").removeClass("location-map-selected");
	var a=mQuery(this).closest("tr").attr("data-marker");
	if(a){
		mQuery(this).closest("tr").addClass("location-map-selected");
		e.openMarker(a);
		e.openDirections(a)
		}
		return false
	})
}
getIconHtml=function(a){
	return "<img src='http://maps.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png'>"
	};
	
getIconMarker=function(a){
	return {
		icon:null,
		shadow:null
	}
};

getIconPicker=function(c,b,a){
	a(null)
		}
	}
LocationMap.ajax=function(c,b,a){
	mQuery.ajax({
		type:c,
		cache:false,
		url:ajaxurl,
		data:b,
		success:function(b){
			if(b.status=="OK"){
				a(b);
				return
			}else if(b){
				alert(mapl10n.ajax_error+"\r\n"+b);
				a(b);
				return
			}
		},
	error:function(d,b,a){
		if(typeof a=="undefined")return;
		var c=mapl10n.ajax_error+"\r\nStatus="+b+"\r\n"+a;
		alert(c);
		return
	}
	})
};

LocationMap.ajaxMapCreate=function(a,b){
	var c={
		action:"eventmap_map_create",
		postid:a.postid
		};
		
	LocationMap.ajax("POST",c,function(c){
		if(c.status=="OK"){
			var d=new LocationMap(c.data.map,a);
			b(d)
			}
		})
};

LocationMap.ajaxMapDelete=function(b,a){
	!b&&a(true);
	var c={
		action:"eventmap_map_delete",
		mapid:b
	};
	
	LocationMap.ajax("POST",c,function(b){
		b.status=="OK"&&a()
		})
	};
	
LocationMap.parseAddress=function(a){
	if(!a||a=="")return{
		firstLine:"",
		secondLine:""
	};
	
	if(a.lastIndexOf(", USA")>0){
		a=a.slice(0,a.lastIndexOf(", USA"));
		if(a.indexOf(",")==a.lastIndexOf(","))return{
			firstLine:a,
			secondLine:""
		}
		}
		return a.indexOf(",")==-1?{
	firstLine:a,
	secondLine:""
}:{
	firstLine:a.slice(0,a.indexOf(",")),
	secondLine:a.slice(a.indexOf(", ")+2)
	}
};

LocationMap.stringToLatLng=function(b){
	var a={
		title:null,
		latLng:null
	};
	
	if(b.lastIndexOf("@")!==-1){
		a.title=b.substr(0,b.lastIndexOf("@")).replace(/^\s+|\s+$/g,"");
		b=b.substr(b.lastIndexOf("@")+1)
		}
		var c=b.split(",",2),d=Number(c[0]),e=Number(c[1]);
	if(isNaN(d)||isNaN(e))return false;
	a.latLng=new google.maps.LatLng(d,e);
	a.title=a.title?a.title:a.latLng.toUrlValue();
	return a
	};
	
function LocationMapEditor(j,o){
	for(var b=null,c=o,a=[],i=0;i<j.length;i++)
		a.push(new LocationMap(j[i],c));
	
	mQuery(document).ready(function(){
			p();
			m(0);
	});
	function p(){
		g();
		mQuery("#location_map_metabox").show();
		mQuery("#publish").click(function(){
			h()
			});
		mQuery("#post-preview").click(function(){
			h()
			});
		mQuery("#location_map_create_btn").click(function(){
			k();
			return false
			});
		mQuery("#location_map_save_btn").click(function(){
			h();
			return false
			});
		mQuery("#location_map_recenter_btn").click(function(){
			a[b].recenter(null,false);
			return false
			});
		mQuery("#location_map_saddr").keypress(function(a){
			if(a.which==13){
				a.preventDefault();
				mQuery("#location_map_add_btn").click();
				return false
				}
				return true
			});
		mQuery("#location_map_add_btn").click(function(){
			var d=mQuery("#location_map_saddr"),e=mQuery("#location_map_saddr_corrected"),c=LocationMap.stringToLatLng(d.val());
			if(c){
				a[b].addPOI({
					title:c.title,
					body:"",
					address:null,
					correctedAddress:null,
					point:{
						lat:c.latLng.lat(),
						lng:c.latLng.lng()
						},
					iconid:null,
					viewport:null
				});
				return
			}
			a[b].geoCode(d,e,function(c){
				var f=LocationMap.parseAddress(c[0].formatted_address),e;
				if(c[0].geometry.viewport)e={
					sw:{
						lat:c[0].geometry.viewport.getSouthWest().lat(),
						lng:c[0].geometry.viewport.getSouthWest().lng()
						},
					ne:{
						lat:c[0].geometry.viewport.getNorthEast().lat(),
						lng:c[0].geometry.viewport.getNorthEast().lng()
						}
					};
				
			a[b].addPOI({
				title:f.firstLine,
				body:f.secondLine,
				address:d.val(),
				correctedAddress:c[0].formatted_address,
				point:{
					lat:c[0].geometry.location.lat(),
					lng:c[0].geometry.location.lng()
					},
				iconid:null,
				viewport:e
			})
			})
		})
	}
	function g(){
	var c="";
	if(a.length>0){
		c+="<table>";
		for(var b=0;b<a.length;b++){
			jQuery('#location_map_create_btn').hide();
			var d=a[b].getTitle();
			var post_id = a[b].getPostId();
			c+="<tr data-idx='"+b+"' data-postid='"+post_id+"'><td><b><a href='#' class='location_map_title' data-idx='"+b+"' data-postid='"+post_id+"'>"+d+"</a></b><div class='location-map-maplist-links' style='visibility:hidden'><a href='#' class='location-maplist-edit' data-idx='"+b+"' data-postid='"+post_id+"'>"+mapl10n.edit+"</a> | <a href='#' class='location-maplist-delete' data-idx='"+b+"' data-postid='"+post_id+"'>"+mapl10n.del+"</a></div></td></tr>"
			}
			c+="</table>"
		}
		mQuery("#location_map_maplist").html(c);
	mQuery("#location_map_maplist tr").hover(function(){
		mQuery(this).find(".location-map-maplist-links").css("visibility","visible")
		},function(){
		mQuery(this).find(".location-map-maplist-links").css("visibility","hidden")
		});
	mQuery(".location_map_title").click(function(){
		var a=mQuery(this).attr("data-idx");
		m(a);
		return false
		});
	mQuery(".location-maplist-edit").click(function(){
		var a=mQuery(this).attr("data-idx");
		n(a);
		return false
		});
	mQuery(".location-maplist-delete").click(function(){
		var a=mQuery(this).attr("data-idx");
		l(a);
		return false
		})
	}
	function k(){
	LocationMap.ajaxMapCreate(c,function(c){
		a.push(c);
		b=a.length-1;
		e(true);
		a[b].display(function(){
			d(true)
			})
		})
	}
	function h(){
	if(b===null||mQuery("#location_map_adjust_panel").is(":hidden"))return;
	mQuery("#location_map_title").val()==""&&mQuery("#location_map_title").val(mapl10n.untitled);
	a[b].setTitle(mQuery("#location_map_title").val());
	a[b].ajaxMapSave(c.postid,function(){
		e(false);
		g()
		})
	}
	function m(c){
	if(b===c)return;
	if(typeof a[c] !='undefined')
	{
		a[c].display(function(){
			b=c;
			d(true)
			})
	}
	}
	function n(c){
	if(b===c){
		e(true);
		d(true);
		return
	}
	a[c].display(function(){
		b=c;
		e(true);
		d(true)
		})
	}
	function l(c){
	b=c;
	confirm(mapl10n.delete_map_prompt)&&LocationMap.ajaxMapDelete(a[b].getMapid(),function(){
		a.splice(b,1);
		b=null;
		d(false);
		jQuery('#location_map_create_btn').show();
		g()
		})
	}
	function d(c){
	if(c){
		mQuery("#location_map_0").show();
		f(a[b].getWidth(),a[b].getHeight())
		}else mQuery("#location_map_0").hide()
		}
	function e(d){
	if(d){
		mQuery("#location_map_title").val(a[b].getTitle());
		var c=a[b].getMapid()?a[b].getMapid():"New";
		mQuery("#location_map_add_panel").css("visibility","visible");
		mQuery("#location_map_maplist_panel").hide();
		mQuery("#location_map_adjust_panel").show();
		a[b].setEditingMode(true)
		}else{
		mQuery("#location_map_add_panel").css("visibility","hidden");
		mQuery("#location_map_maplist_panel").show();
		mQuery("#location_map_adjust_panel").hide();
		a[b].setEditingMode(false)
		}
		mQuery("#location_map_saddr").removeClass("location-map-address-error");
	mQuery("#location_map_saddr").val("");
	mQuery("#location_map_saddr_corrected").html("")
	}
	function f(e,d){
	document.getElementById(c.mapName).style.width=e+"px";
	document.getElementById(c.mapName).style.height=d+"px";
	if(typeof Prototype!="undefined")
		{
			document.getElementById("location_map_0_poi_list").style.height=d-$("location_map_adjust").getDimensions().height-12+"px";
		}
	else
		{
			mQuery("#location_map_0_poi_list").height(d-mQuery("#location_map_adjust").height()-12+"px");
		}
	a[b].resize()
	}
}