      rootURL = "";
      u = "";
      function setRoot(){
          
          address_url = window.location.href;
          var hash = address_url.lastIndexOf("#");
          if(window.location.hash){
            address_url = address_url.replace(window.location.hash,'');
          }
          if(address_url.substr(hash)==="#"){
            address_url = address_url.substr(0,hash);
          }

          links = $('a');

          array_Address_url = address_url.split("/");
          resources = [];
          actions = [];
          indexes = [];
          profiles = [];
          isResource = false;
          isAction = false;
          isId = false;
          isProfile = false;
          actions = ['new','edit'];
          resources = ['local','realty','route','user','profile','album'];
          configs = ['config'];

          function whatIs(arrayI){
              if(arrayI=='user'){
                  return 'isUser';
                
              }else if(configs.indexOf(arrayI)!=-1){
                  return 'isConfig';
                
              }else if(resources.indexOf(arrayI)!=-1){
                  return 'isResource';
               
              }else if(actions.indexOf(arrayI)!=-1){
                  return 'isAction';   
               
              }else if(arrayI!=='' && !isNaN(arrayI)){
                  return 'isNum' ; 
                
              }else{
                  return false;   
              }
          }

          for(var i=0 ,l = (array_Address_url.length);i<l;i++){

              if(whatIs(array_Address_url[i])!==false){
                  indexes.push(whatIs(array_Address_url[i]));   
              }
          }
              
          function defineRoot(u){

              if(u==='isUser/isAction'){
                  rootURL = '../'; 
              }else if(u==='isUser/isConfig'){
                  rootURL = '../'; 
              }else if(u==='isResource/isAction'){
                  rootURL = '../../';
              }else if(u==='isResource/isNum'){
                  rootURL = '../../';
              }else if(u==='isResource/isAction/isNum'){
                  rootURL = '../../';
              }else if(u==='/'){
                  rootURL = ''; 
              }
          }

          u = indexes.join('/') ;
          defineRoot(u);

          links.each(function(i,it){
              $el = $(it);

              try{
                  if(!$el.attr('href').toString().contains('google')){
                      str = $el.attr('href').toString();
                      $el.attr('href',rootURL+str);  
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
      } //fim setRoot


      $('.container').ready(function(){
          console.log('container');
          setRoot();
      });
