     
      links = $('a');
      links.each(function(i,it){
          $el = $(it);

          try{
              if(!$el.attr('href').toString().contains('google')){
                  str = $el.attr('href').toString();
                  //console.log(str)
                  $el.attr('href',rootURL+str);  
                  //console.log($el.attr('href'))
              }
          }catch(e){
             
          }
      });
      if($('.header_avatar').length > 0){
          $el = $('.header_avatar');
          src = $el.attr('src').toString();
          $el.attr('src',rootURL+src);
      }

      if($(".avatar").length > 0){
          $el2 = $(".avatar");
          src2 = $el2.attr('src').toString();
          $el2.attr('src',rootURL+src2);
      }

      if($("#formSearch").length > 0){
          $el3 = $("#formSearch");
          src3 = $el3.attr('action').toString();
          $el3.attr('action',rootURL+src3);
      }
      if($(".local_album_status_add").length > 0){
          $el4 = $(".local_album_status_add");
          src4 = $el4.attr('src').toString();
          $el4.attr('src',rootURL+src4);
      }
      if($(".local_album_status_dll").length > 0){
          $el5 = $(".local_album_status_dll"); 
          src5 = $el5.attr('src').toString();
          $el5.attr('src',rootURL+src5);
      }
      if($(".realty_album_status_add").length > 0){
          $el6 = $(".realty_album_status_add");
          src6 = $el6.attr('src').toString();
          $el6.attr('src',rootURL+src6);
      }
      if($(".realty_album_status_dll").length > 0){
          $el7 = $(".realty_album_status_dll"); 
          src7 = $el7.attr('src').toString();
          $el7.attr('src',rootURL+src7);
      }
      if($(".link_fb").length > 0){
          $el8 = $(".link_fb"); 
          src8 = $el8.attr('src').toString();
          $el8.attr('src',rootURL+src8);
      }
      if($(".route_album_status_add").length > 0){
          $el9 = $(".route_album_status_add");
          src9 = $el9.attr('src').toString();
          $el9.attr('src',rootURL+src9);
      }
      if($(".route_album_status_dll").length > 0){
          $el10 = $(".route_album_status_dll"); 
          src10 = $el10.attr('src').toString();
          $el10.attr('src',rootURL+src10);
      }
      //$('html').ready(function(){
      //    console.log('container');
      //    setRoot();
      //});