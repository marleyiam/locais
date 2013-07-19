       //window.alert('!');
      rootURL = "";
      function setRoot(){
          
          rootURL = "";
          address_url = window.location.href
          links = $('a')

          array_Address_url = address_url.split("/")
          console.log(array_Address_url);
          resources = []
          actions = []
          indexes = []
          isResource = false;
          isAction = false;
          isId = false;
          actions = ['new','edit']
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
                  rootURL = '../';
              }else if(u==='isResource/isAction/isNum'){
                  rootURL = '../../';
              }else if(u==='/'){
                  rootURL = ''; 
              }
              console.log(rootURL);
          }

          u = indexes.join('/') ;
          console.log(indexes);
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
              src = $('.header_avatar').attr('src').toString();
              $('.header_avatar').attr('src',rootURL+src);
          }

          if($(".avatar").length > 0){
              src2 = $(".avatar").attr('src').toString();
              $(".avatar").attr('src',rootURL+src2);
          }

          if($("#aformSearch").length > 0){
              src3 = $(".avatar").attr('action').toString();
              $("#formSearch").attr('src',rootURL+src3);
          }
          if($(".local_album_status_add").length > 0){
              src4 = $(".local_album_status_add").attr('src').toString();
              $(".local_album_status_add").attr('src',rootURL+src4);
          }
          if($(".local_album_status_dll").length > 0){
              src4 = $(".local_album_status_dll").attr('src').toString();
              $(".local_album_status_dll").attr('src',rootURL+src4);
          }
      } //fim setRoot


      $('#main').ready(function(){
          console.log('ready');
          setRoot();
      });

       Array.prototype.contains = function(key, val, param) {
           var i = this.length;
           while (i--) {
              if(this[i][key] == param) {
                  return this[i][val];
              }
           }
           return false;
       }

      Array.prototype.remove = function(obj) {
        for(var i in this){
          try{
            if(this[i].location.jb == obj.location.jb && this[i].location.kb == obj.location.kb){
              this.splice(i, 1);
            }
          }catch(e){

          }
        }
        var reindexed = this.filter(function (item) { return item != undefined });
        return reindexed;
      }

      //função que retorna o tamanho de uma coleção
      Object.size = function(obj) {
          var size = 0, key;
          for (key in obj) {
              if (obj.hasOwnProperty(key)) size++;
          }
          return size;
      }; 

      /*
      function fa(){
       $('#origem').trigger('keydown'); 
        $('#origem:focus').autocomplete('focus', $('#origem').val('bairro nordeste') ); 

      }

      function fc(){
        $(".ui-menu-item").trigger('click');   
      }
      setInterval(fa(),fc(),500,500);
     '*/
     $(".add_btn").on('click', function(e){
         e.preventDefault();

             user_a = $(".header_avatar").attr("data-user");
             user_b = $(".avatar").attr("data-public-user");
             $.ajax({
             type: 'post',
             url: 'http://localhost/locais_fotos/add_user',
             data: {from_user:user_a,to_user:user_b},
             success: function(data){
                 window.alert(data);
             },
             error: function(jqxhr){
                 window.alert(jqxhr);
             }
         });
     });

     //$("#target").val()

          /** Adiciona o fomr_rota*/
           url = window.parent.location.href;
           url_array = url.split("/");
           acao = url_array[4]; 
           $.inArray(acao, url_array)
           v = '/local/new/';
           
          String.prototype.contains = function(it) { 
              return this.indexOf(it) != -1; 
          };
           
           console.log(url.contains(v));

           $(document).ready(function(){

            /** ADD USER */
            $(".add_btn").on('click', function(e){
                e.preventDefault();

                    user_a = $(".header_avatar").attr("data-user");
                    user_b = $(".avatar").attr("data-public-user");
                    $.ajax({
                    type: 'post',
                    url: 'http://localhost/locais_fotos/add_user',
                    data: {from_user:user_a,to_user:user_b},
                    success: function(data){
                        window.alert(data);
                    },
                    error: function(jqxhr){
                        window.alert(jqxhr);
                    }
                });
            });

              /** ACC FRIEND REQUEST */
              $("#acc").on('click',function(e){
                  id = $(this).attr('data-friend');
                  e.preventDefault();
                      $.ajax({
                      type: 'post',
                      url: 'requests/confirm',
                      data: {id:id},
                      success: function(data){
                        //console.log(data);
                        window.alert(data);  
                      },
                      error: function(jqxhr){
                        console.log(jqxhr);    
                      }
                  });
              });

              /** DENY FRIEND REQUEST */
              $("#dny").on('click',function(e){
                  id = $(this).attr('data-friend');
                  e.preventDefault();
                      $.ajax({
                      type: 'post',
                      url: 'requests/deny',
                      data: {id:id},
                      success: function(data){
                        //console.log(data);
                        window.alert(data);
                        $(this).parent('tr').hide("slow"); 
                      },
                      error: function(jqxhr){
                        console.log(jqxhr);
                        $(this).parent('tr').hide("slow");  
                      }
                  });
              });

              /** USER AUTOCOMPLETE */
            /*$.ui.autocomplete.prototype._renderItem = function (ul, item) { 

              var avatar = item.avatar.name? item.avatar.name : item.avatar;
              var inner_html = '<a href="http://localhost/locais_fotos/profile/'+item.id+'">';
              inner_html += '<div class="list_item_container">';
              inner_html += '<div class="image">';
              inner_html += '<img height="50px" width="50px"';
              inner_html += ' src="http://localhost/locais_fotos/uploads_users/' + avatar + '">';
              inner_html += '</div>';
              inner_html += '<div class="label">' + item.name + '</div>';
              inner_html += '<div class="city">' + item.city + '</div>';
              inner_html += '</div></a>';
                      return $( "<li></li>" )
                          .data("item.autocomplete", item)
                          .append(inner_html)
                          .appendTo( ul );
            };

            $("#search_users").autocomplete({
              minLength: 0,
              source: 'http://localhost/locais_fotos/ajax_search_users',
               focus: function(event, ui) {
              },
              select: function(event, ui) {
              }
            });*/

            $("#login-email").change(function(){
                $(".validation").remove();
                email = $(this).val();
                console.log(email);
                $.ajax({
                type: 'post',
                url: 'check_email',
                data: {email:email},
                success: function(data){
                    console.log(data);
                    $validation = $('<tr class="validation">').html(data).css("visibility","visible");
                    $("#login-email").parent().parent().after($validation);
                },
                error :function(jqxhr){
                    console.log(jqxhr);
                }
                });
            });

            /** ADD local to Album */
            $(".btn_add_album").on('click',function(e){
             e.preventDefault();
             $btn = $(this);
             local_id = $btn.attr('data-id-local');
             album_id = $btn.attr('data-id-album');
              //console.log(local_id);
              $.ajax({
                  type: 'post',
                  url: rootURL+'add_local_to_album',
                  data: {local_id:local_id,album_id:album_id},
                  success: function(data){
                      window.alert(data);
                      $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletDLL.png');
                    
                  },
                  error: function(jqxhr){
                      window.alert(jqxhr)
                  }
              });
            });

            /** RMV local of Album */
            $(".btn_rmv_album").on('click',function(e){
             e.preventDefault();
             //console.log('click');
             $btn = $(this);
             local_id = $btn.attr('data-id-local');
             album_id = $btn.attr('data-id-album');

              $.ajax({
                  type: 'post',
                  url: rootURL+'del_local_of_album',
                  data: {local_id:local_id,album_id:album_id},
                  success: function(data){
                      window.alert(data);
                      $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletADD.png');
                
                  },
                  error: function(jqxhr){
                      window.alert(jqxhr)
                  }
              });
            });

           }); // fim do document.ready
