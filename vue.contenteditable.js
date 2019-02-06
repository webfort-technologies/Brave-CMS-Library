/*Vue.component('editable',{
  template:'<div contenteditable="true" @input="update"></div>',
  props:['content'],
  mounted:function(){
    this.$el.innerHTML = this.content;
  },
  methods:{
    update:function(event){
      this.$emit('update',event.target.innerHTML);
    }
  }
})

//@keydown.enter.prevent="$emit('pressedenter',itemindex)" 

@input="$emit('update:value', $event.target.innerHTML)"




          computed: {
            inputListeners: function () {
              var vm = this
              // `Object.assign` merges objects together to form a new object
              return Object.assign({},
                // We add all the listeners from the parent
                this.$listeners,
                // Then we can add custom listeners or override the
                // behavior of some listeners.
                {
                  // This ensures that the component works with v-model
                  input: function (event) {
                    vm.$emit('input', event.target.value)
                  }
                }
              )
            }
          },


*/

// Content editable component
    Vue.component('editable', 
    {
        inheritAttrs: false,
        data : function()
        {
          return { 
            deleteCount : 0 
          }
        },
        template: `<div
          placeholder="Advance Search.." 
         contenteditable="true"       
         
           v-on="inputListeners"
        ></div>`,
        props: ['value','content','itemindex'],
        mounted: function () {
            this.$el.innerHTML = this.value;
        },
        watch: {
            value: function () {
              if(this.$el.innerHTML!=this.value)
              {
                this.$el.innerHTML = this.value;
                placeCaretAtEnd(this.$el);
              } 
            }
        },
              computed: {
            inputListeners: function () {
              var vm = this
              // `Object.assign` merges objects together to form a new object
              return Object.assign({},
                // We add all the listeners from the parent
                this.$listeners,
                // Then we can add custom listeners or override the
                // behavior of some listeners.
                {
                  // This ensures that the component works with v-model
                  input: function (event) {
                     vm.$emit('input', event.target.innerHTML)
                  }
                  /*"keydown.down": function (event) {
                    console.log(event);
                     vm.$emit('keydown', event.target)
                  }*/
                }
              )
            }
          },

        methods:
        {

        

          up : function()
          {
             $(':focus').parent().prev().find("div[contenteditable='true']").focus();
              placeCaretAtEnd($(':focus')[0]);
          },
          down : function()
          {
            $(':focus').parent().next().find("div[contenteditable='true']").focus();
            placeCaretAtEnd($(':focus')[0]);
          },
          deleteCheck : function()
          {
              if(this.$el.innerText=="" && this.content=="")
              {
                this.deleteCount++;
                if(this.deleteCount>0)
                {
                  this.$emit('elementdelete',this.itemindex);
                }
              }
              else
              {
                this.deleteCount=0;
              }
          } // deleteCount()

        }// methods end

    });


function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}