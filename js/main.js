       //window.alert('!');
      rootURL = "";
      u = "";
      
      function setRoot(){
          //links = $('a');
          address_url = window.location.href;
          var hash = address_url.lastIndexOf("#");
          if(window.location.hash){
            address_url = address_url.replace(window.location.hash,'');
          }
          if(address_url.substr(hash)==="#"){
            address_url = address_url.substr(0,hash);
          }

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
                  rootURL = '../';
              }else if(u==='isResource/isAction/isNum'){
                  rootURL = '../../';
              }else if(u==='/'){
                  rootURL = ''; 
              }
          }

          u = indexes.join('/') ;
          defineRoot(u);

          /*links.each(function(i,it){
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
          }*/
      } //fim setRoot


      //$('.container').ready(function(){
          console.log('container');
          setRoot();
      //});

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

          /** Adiciona o form_rota*/
           url = window.parent.location.href;
           url_array = url.split("/");
           acao = url_array[4]; 
           $.inArray(acao, url_array)
           v = '/local/new/';
           
          String.prototype.contains = function(it) { 
              return this.indexOf(it) != -1; 
          };
           
           //console.log(url.contains(v));

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

            /** Email availability*/
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
                  $el = $(this);
                    link = $el.attr('href');
          
                    $.ajax({
                        type: 'get',
                        url: rootURL+link,
                        success: function(data){
                            if(data.status===true){
                              window.alert(data.msg);
                              $el.parent().parent().parent().remove()
                            }else{
                              window.alert(data.msg);
                            }
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

            /*****************************/
              /** ADD route to Album */
               $(".routes_to_album").on('click','.btn_add_route_album',function(e){
                e.preventDefault();
                $btn = $(this);
                route_id = $btn.attr('data-id-route');
                album_id = $("#album_name").attr('data-id-album');

                $.ajax({
                    type: 'post',
                    url: rootURL+'add_route_to_album',
                    data: {route_id:route_id,album_id:album_id},
                    success: function(data){
                        window.alert(data);
                        $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletDLL.png');
                        $prnt = $btn.parent();
                        $prnt.find('button').remove();
                        $prnt.append('<button data-id-route="'+route_id+'" class="btn_rmv_route_album">Remover do Album</button>');
                    },
                    error: function(jqxhr){
                        window.alert(jqxhr)
                    }
                });
              });

              /** RMV route of Album */
              $(".routes_to_album").on('click','.btn_rmv_route_album',function(e){
                e.preventDefault();
                $btn = $(this);
                route_id = $btn.attr('data-id-route');
                album_id = $("#album_name").attr('data-id-album');

                $.ajax({
                    type: 'post',
                    url: rootURL+'del_route_of_album',
                    data: {route_id:route_id,album_id:album_id},
                    success: function(data){
                        window.alert(data);
                        $btn.parent().parent().find('img').attr('src',rootURL+'img/bulletADD.png');
                        $prnt = $btn.parent();
                        $prnt.find('button').remove();
                        $prnt.append('<button data-id-route="'+route_id+'" class="btn_add_route_album" data-id-album="'+album_id+'">Adicionar ao Album</button>');
                    },
                    error: function(jqxhr){
                        window.alert(jqxhr)
                    }
                });
              });
            /***************************/

            /** Clone */
            $("tr").on('click','.btnClone', function(e){
              current_imgs = [];
              $imgs = $(".current_imgs");
              $imgs.each(function(i,it){
                  $el = $(it);
                  path = $el.attr('src');
                  token = path.lastIndexOf("/");
                  current_img_name = path.substr(token+1);
                  coma = i <= ($imgs.length-2)? ',':'';
                  current_imgs[i] = '"'+current_img_name+'"';
              });
           
                dataObj = '{';
                dataObj += '"clone_type"'+':'+'"'+$(this).attr('data-clone')+'"';
                dataObj += ',';
                list = $(".resource_data").find('td');
                list.each(function(i,it){
                  $el = $(it);
                  if($el.attr('id')!==undefined){
                    resource_id =  $el.attr('id').toString();
                    token = resource_id.lastIndexOf('_');
                    field = resource_id.substr(token+1);
                    dataObj += '"'+field+'"'+':'+'"'+$el.text()+'"';
                    dataObj += ',';
                  }
                });
                dataObj += '"imgs[]"'+':['+current_imgs+']';
                dataObj += '}';
                dataObj = JSON.parse(dataObj);

              $.ajax({
                type: 'post',
                url: rootURL+'clone',
                data: dataObj,
                success: function(data){
                  window.alert(data);
                },
                error: function(jqxhr){
                  console.log(jqxhr);
                }
              });
            });

            /** REMOVE PIC from Resource */
            $("#imgs").on('click','.pic_close',function(){
                $el = $(this);
                $.ajax({
                    type: 'post',
                    url: rootURL+'rmv_picture',
                    data: {img_id:$el.attr('data-id-img'),resource:$el.attr('data-resource')},
                    success: function(data){
                      $el.parent().remove();
                      window.alert(data);
                    },
                    error: function(jqxhr){
                      console.log(jqxhr);
                    }
                });
            });

            /** REMOVE Resource */
            $(".rmv_resource").on('click','.rmv_resource_link',function(e){
                e.preventDefault();
                if (confirm("Tem certeza que deseja excluir esse registro ?")) {
                    link = $(this).attr('href');
                    $.ajax({
                        type: 'get',
                        url: rootURL+link,
                        success: function(data){
                            window.alert("Registro excluído com sucesso !");
                        },
                        error: function(jqxhr){
                            window.alert(jqxhr);
                        }
                    });
                }
                return false;
            });

                    /** USER AUTOCOMPLETE */
            if(u==='isUser' || window.location.href.contains('profile')){
                
              $.ui.autocomplete.prototype._renderItem = function (ul, item) { 
                
                var avatar = item.avatar.name? item.avatar.name : item.avatar;
                var inner_html = '<a href="'+rootURL+'profile/'+item.id+'">';
                inner_html += '<div class="list_item_container">';
                inner_html += '<div class="image">';
                inner_html += '<img height="50px" width="50px"';
                inner_html += ' src="'+rootURL+'uploads_users/' + avatar + '">';
                inner_html += '</div>';
                inner_html += '<div class="label">' + item.name + '</div>';
                inner_html += '<div class="city">' + item.city + '</div>';
                inner_html += '</div></a>';
                        return $("<li></li>")
                            .data("item.autocomplete", item)
                            .append(inner_html)
                            .appendTo( ul );
              };
             
                $("#search_users").autocomplete({
                  minLength: 0,
                  source: rootURL+'ajax_search_users',
                   focus: function(event, ui) {
                  },
                  select: function(event, ui) {
                  }
                });
            }


            $("#forgot").click(function(e){
                e.preventDefault();
                $("#formmail").show("slow");
            });

            $("#formmail").submit(function(e) {
              e.preventDefault();
              email = $("#sendmail").val();
              $.ajax({
                type: 'post',
                data: {email:email},
                url: $("#formmail").attr('action'),
                success: function(data){
                  window.alert(data)
                },
                error: function(jqxhr){
                  window.alert(jqxhr)
                }
              });
            });

           }); // fim do document.ready