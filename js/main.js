       //window.alert('!');
      rootURL = "";
      u = "";
      function setRoot(){
          
          rootURL = "";
          address_url = window.location.href;
          links = $('a');

          array_Address_url = address_url.split("/");
          //console.log(array_Address_url);
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
            //console.log(u);
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
              //console.log(rootURL);
          }

          u = indexes.join('/') ;
          //console.log(indexes);
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

          if($("#aformSearch").length > 0){
              $el3 = $("#aformSearch");
              src3 = $el3.attr('action').toString();
              $el3.attr('src',rootURL+src3);
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


      $('#main').ready(function(){
          //console.log('READY');
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

          /** Adiciona o form_rota*/
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
                    url: rootURL+'add_user',
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
             $(".locals_to_album").on('click','.btn_add_album',function(e){
              e.preventDefault();
              $btn = $(this);
              local_id = $btn.attr('data-id-local');
              album_id = $("#album_name").attr('data-id-album');

              $.ajax({
                  type: 'post',
                  url: rootURL+'add_local_to_album',
                  data: {local_id:local_id,album_id:album_id},
                  success: function(data){
                      window.alert(data);
                      $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletDLL.png');
                      $prnt = $btn.parent();
                      $prnt.find('button').remove();
                      $prnt.append('<button data-id-local="'+local_id+'" class="btn_rmv_album">Remover do Album</button>');
                  },
                  error: function(jqxhr){
                      window.alert(jqxhr)
                  }
              });
            });

            /** RMV local of Album */
            $(".locals_to_album").on('click','.btn_rmv_album',function(e){
              e.preventDefault();
              $btn = $(this);
              local_id = $btn.attr('data-id-local');
              album_id = $("#album_name").attr('data-id-album');

              $.ajax({
                  type: 'post',
                  url: rootURL+'del_local_of_album',
                  data: {local_id:local_id,album_id:album_id},
                  success: function(data){
                      window.alert(data);
                      $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletADD.png');
                      $prnt = $btn.parent();
                      $prnt.find('button').remove();
                      $prnt.append('<button data-id-local="'+local_id+'" class="btn_add_album" data-id-album="'+album_id+'">Adicionar ao Album</button>');
                  },
                  error: function(jqxhr){
                      window.alert(jqxhr)
                  }
              });
            });

            /** REMOVE ALBUM */
            $(".rmv_album").on('click','.rmv_album_link',function(e){
                e.preventDefault();
                if (confirm("Tem certeza que deseja excluir esse album ?")) {
                    link = $(this).attr('href');
                    $.ajax({
                        type: 'get',
                        url: rootURL+link,
                        success: function(data){
                            window.alert(data);
                        },
                        error: function(jqxhr){
                            window.alert(jqxhr);
                        }
                    });
                }
                return false;
            });


            /** ADD Realty to Album */
            $(".realties_to_album").on('click','.btn_add_realty_album',function(e){
              e.preventDefault();
              $btn = $(this);
              realty_id = $btn.attr('data-id-realty');
              album_id = $("#album_name").attr('data-id-album');

              $.ajax({
                  type: 'post',
                  url: rootURL+'add_realty_to_album',
                  data: {realty_id:realty_id,album_id:album_id},
                  success: function(data){
                      window.alert(data);
                      $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletDLL.png');
                      $prnt = $btn.parent();
                      $prnt.find('button').remove();
                      $prnt.append('<button data-id-realty="'+realty_id+'" class="btn_rmv_realty_album">Remover do Album</button>');
                  },
                  error: function(jqxhr){
                      window.alert(jqxhr)
                  }
              });
            });

            /** RMV Realty of Album */
            $(".realties_to_album").on('click','.btn_rmv_realty_album',function(e){
              e.preventDefault();
              $btn = $(this);
              realty_id = $btn.attr('data-id-realty');
              album_id = $("#album_name").attr('data-id-album');

              $.ajax({
                  type: 'post',
                  url: rootURL+'del_realty_of_album',
                  data: {realty_id:realty_id,album_id:album_id},
                  success: function(data){
                      window.alert(data);
                      $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletADD.png');
                      $prnt = $btn.parent();
                      $prnt.find('button').remove();
                      $prnt.append('<button data-id-realty="'+realty_id+'" class="btn_add_realty_album" data-id-album="'+album_id+'">Adicionar ao Album</button>');
                  },
                  error: function(jqxhr){
                      window.alert(jqxhr)
                  }
              });
            });

           }); // fim do document.ready
