var template =  `<span class="btn btn-success btn-lg btn-file">
                            Upload Photos <input type="file" @change="onFileChanged" multiple>
                </span>`;

Vue.component('multiple-image-selector', {
    props: ['value'],
    data: function () {
        return {
            fileArray : this.value
        }
    },
    template: template,
    methods:{
        onFileChanged(){
            let reader;
            for (var i=0; i < event.target.files.length; i++) {
                reader = new FileReader();
                reader.onload = (function(self,file) {
                    return function(e) {
                        self.fileArray.push({
                            order: Math.floor((Math.random() * 100) + 1),
                            caption : '',
                            imageUrl : '',
                            base64 : e.target.result
                        });
                    };
                })(this,event.target.files[i]);
                reader.readAsDataURL(event.target.files[i]);
            }
        },
    },
    computed : {
        
    },
    watch : {
        
    },
    mounted(){
        
    }
})