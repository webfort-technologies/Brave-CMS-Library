/*
Bulk Checkbox Selection and the display of the Bulk Action buttons. 
*/
$(document).ready(function(){    
        $(".checkboxID").change(function () {
          var check = $('input.checkboxID[type=checkbox]:checked').length;
          if(check>0)
          {
            $("#bulkActionBox").slideDown();
            $("#checkBoxCount").html(check); 
          }
          else
          {
            $("#bulkActionBox").slideUp(); 
          }
           return false;
        });
        $("#selectAll").change(function() {
            if(this.checked) {
                $('input.checkboxID[type=checkbox]').prop('checked', true);
            }
            else
            {
                $('input.checkboxID[type=checkbox]').prop('checked', false);
            }
            $(".checkboxID").trigger("change");
        });
      })