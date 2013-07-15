       //window.alert('!');
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
            $.ui.autocomplete.prototype._renderItem = function (ul, item) { 

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
            });


            //$("[name=email]:eq(1)").change(function(){
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

           }); // fim do document.ready
