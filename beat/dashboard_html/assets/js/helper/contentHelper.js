		
		function imgError(div){
			$(div).attr('src',basedomainpath+"public_assets/user/photo/default.jpg" );
		}
		
		function getbaranklist(data,start){
			$.post(basedomain+'barank/baRankListAjax?startdate='+startdate+'&enddate='+enddate+'&brandid='+brandid+'&sbaid='+sbaid+'&areaid='+areaid+'&activity='+activity+'',{start:start},function(response){
					if(response.data){
						var html='';
						html+="<tr>";
                    	html+="<th>No</th>";
                        html+="<th>Profile Picture</th>";
                        html+="<th>Name</th>";
                        html+="<th>Area</th>";
                        html+="<th>Brand</th>";
                        html+="<th>Activities</th>";
						html+="</tr>";
							$.each(response.data,function(i,e){
								
                    		html+="<tr>";
                        		html+="<td>"+e.no+"</td>";
								html+="<td><img onerror='imgError(this)' src='"+basedomainpath+"public_assets/user/photo/"+e.img+"' style='width:60px;height:60px;' /></td>";
								html+="<td>"+e.name+" "+e.last_name+"</td>";
                        		html+="<td>"+e.city+"</td>";
                        		html+="<td>"+e.brand_name+"</td>";
                        		html+="<td>"+e.totalentourage+"</td>";
                    		html+="</tr>";
								
							})
							
					}
					$('.gridtable').html(html);
			},'json');
		}
		
		function getUserEngagelist(data,start){
			$.post(basedomain+'userDetailEngage/userPagingAjax',{start:start},function(response){
				if(response.data){
					var html='';
						html+="<tr>";
                            html+="<th>Entourage</th>";
                            html+="<th>Engagement</th>";
                        html+="</tr>";
						$.each(response.data,function(i,e){
						
						html+="<tr>";
                            html+="<td>"+e.name+" "+e.last_name+"</td>";
                            html+="<td>"+e.baengagement+"</td>";
                        html+="</tr>";
							
						})
						
				}
				$('.tableDetailEngagement').html(html);
				
			},'json');
		}
		
		function getCityEngagelist(data,start){
			$.post(basedomain+'userDetailCity/cityPagingAjax',{start:start},function(response){
				if(response.data){
					var html='';
						html+="<tr>";
                            html+="<th>Entourage</th>";
                            html+="<th>Engagement</th>";
                        html+="</tr>";
						$.each(response.data,function(i,e){
							
						html+="<tr>";
                            html+="<td>"+e.name+" "+e.last_name+"</td>";
                            html+="<td>"+e.baengagement+"</td>";
                        html+="</tr>";
							
						})
						
				}
				$('.tableDetailEngagement').html(html);
				
			},'json');
		}
		
		function showarticledetail(id,articleType){
			$('.commentsContainer').show();
			$('.popupDetail').hide();
			$(".popupLoader").append('<div class="loaders"><img src="'+basedomain+'assets/images/loader.gif"></div>');
			$('.popupmsg').remove();
			
			if(!articleType) articleType = pages;
			$.post(basedomain+articleType+'/detail',{id:id},function(data){
			
				var html="";
				
				if(data.result){
					var article = data.article;
					if(article){
						$('.popupDetail').show();
						
						var imagepath = false;
						if(articleType=="event") {
							imagepath = "event";
						} else if (articleType=="forum") {
							imagepath = "forum";
						}
						
						if(articleType=='music' || articleType=='dj'){
							html+= popupmediahtml(article,articleType);
						} else if (articleType=='forum') {
							html+= popupforumhtml(article,articleType);
						} else{
							html+= popupimagehtml(article,articleType);
						}
						
						$(".popupDetail").html(html);
						
						$('.notes_add').hide();
						$('.notes_addfailed').hide();
						
						if(article.id) 	$('.commentdata').attr("contentid",article.id);
						else $('.commentdata').attr("contentid",0);
					
					}	
				
					
				
					$('.commentdata').html('');
					$("#commentpagingID").html("");
					if(data.comment){
						$('.commentdata').append('<div class="loaders"><img src="'+basedomain+'assets/images/loader.gif"></div>');
						var commenthtml = "";
						commenthtml += commentviewshtml(data.comment);
						getpaging(0,article.commentcount,"commentpagingID","paging_comment_ajax",5);
					
						$('.commentdata').html(commenthtml);
					}
					
				}else{
				
					$('.popupDetail').hide();
					$(".popupLoader").append('<div class="popupmsg">there is no content</div>');
				}
				
					/* attendting button */
					if(articleType=='event') {
						$('.attender').html(article.attending);	
						$(".doattending").attr("contentid",article.id);
					}
					
					
					$('.loaders').remove();					
					$('.mp3Player audio').mediaelementplayer({
							audioHeight: 30,
							startVolume: 0.8,
							loop: false,
							enableAutosize: true,
							features: ['playpause','current','progress']
							});
			},"JSON");
		}
		
		function showeditarticledetail(id,articleType){
			$('.editpopupDetail').hide();
			$(".popupLoader").append('<div class="loaders"><img src="'+basedomain+'assets/images/loader.gif"></div>');
			$('.popupmsg').remove();
			
			if(!articleType) articleType = pages;
			$.post(basedomain+articleType+'/detail',{id:id},function(data){
			
				var html="";
				
				if(data.result){
					var article = data.article;
					
					if(article){
						$('.editpopupDetail').show();
						
						var imagepath = false;
						if(articleType=="event") imagepath = "event";						
						html+= editpopupcontenthtml(article,articleType);
						
						$(".editpopupDetail").html(html);					
					}	
				}else{
				
					$('.editpopupDetail').hide();
					$(".popupLoader").append('<div class="popupmsg">there is no content</div>');
				}
				
					/* attendting button */
					if(articleType=='event') {
						$('.attender').html(article.attending);	
						$(".doattending").attr("contentid",article.id);
					}
					
					
					$('.loaders').remove();					
					$('.mp3Player audio').mediaelementplayer({
							audioHeight: 30,
							startVolume: 0.8,
							loop: false,
							enableAutosize: true,
							features: ['playpause','current','progress']
							});
			},"JSON");
		}
		
		
		
		$(document).on("click",".articledetail", function(){
			$('.editpopupDetail').hide();
			var id = $(this).attr('contentid');
			var articleType =  $(this).attr('articleType');
			showarticledetail(id,articleType);
		});
		
		$(document).on("click",".editarticledetail", function(){
			$('.popupDetail').hide();
			$('.commentsContainer').hide();
			var id = $(this).attr('contentid');
			var articleType =  $(this).attr('articleType');
			showeditarticledetail(id,articleType);
		});
		
		$(document).on("click",".submit-editContent", function(){
			var cid = 1420;
			//$.post(basedomain+pages+"/editContent",{cid:cid},function(data){
				//if(data){
					var optionEditContent = {
						dataType:  'json',
						success:    function(data) {
							if(data) {
								$("#popup-messagebox .popupContent .entry-popup h3").html("Sukses Update Content");
								$(".messageboxpop").trigger('click');
							}else {
								$("#popup-messagebox .popupContent .entry-popup h3").html("Gagal Update Content");
								$(".messageboxpop").trigger('click');
							}
						}
					};
					$("#form-editPopupContent").ajaxForm(optionEditContent);
				//}
			//},"JSON");
		});
		
		/* ------------------  pagination ------------------- */
		
		function getpaging(start,total_rows,contentPaging,pagingfunction,itemperpage){
				if(start == 0)start=1;
				if(total_rows == 0) total_rows=0;
				kiPagination(total_rows, start, contentPaging, pagingfunction,pagingfunction, itemperpage);
		}
	
		function paging_ajax(fungsi,start){
					$('.article_image_content').html('');
					$('.article_image_content').append('<div class="loaders"><img src="'+basedomain+'assets/images/loader.gif"></div>');
					$.post(basedomain+pages+"/ajax",{start:start,needs:"content"},function(data){
						if(data){
							if(data.result){
								var html = "";
								html+=articleimagehtml(data.result);
								
								$('.loaders').remove();
								$('.article_image_content').html(html);
								
								$(".showPopup").fancybox({
									'titlePosition'		: 'inside',
									'transitionIn'		: 'none',
									'transitionOut'		: 'none'
								});
								
								$(".cbanner0").attr('src',$(".cbanner0").attr("url"));
								var maxlength = ($(".sequenceload").length)-1;
								$(".sequenceload").each(function(i,e){
									$(this).load(function(){
											nextload('.cbanner',i);
										
											if(i==maxlength) $('#photo_gallery').prepend( ".box" ).masonry( 'reload' );
									});
									
								});
																
								
								
							}
						}
					},"JSON");
		}
		

