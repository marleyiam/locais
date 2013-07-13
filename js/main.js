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