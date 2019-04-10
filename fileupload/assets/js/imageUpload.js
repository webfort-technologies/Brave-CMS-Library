var template = `<div class="col-sm-2 pl-0">
                    <!-- image-preview-filename input [CUT FROM HERE]-->
                    <div class="imgUp">
                        <!-- <div class="imagePreview"></div>-->
                        <div class="imagePreview"> 
                            <img :src="sourceImage" />
                        </div>
                        <label class="btn btn-primary btn-primary-custom">
                            Upload<input type="file" class="uploadFile img" value="Upload Photo" ref="fileupload" @change="onFileChanged" style="width: 0px;height: 0px;overflow: hidden;">
                        </label>
                        <i class="fa fa-times del" v-if="deleteButton" @click="deleteAction()"></i>
                    </div><!-- col-2 -->
                    <!--<input type="file" name="myFile" @change="onFileChanged"> 
                    <img :src="file.base64" placeholder="No Image" v-if="">-->
                </div>`;

Vue.component('image-selector', {
    props: ['value', 'file_index', 'type' , 'callback'],
    data: function () {
        return {
            file : this.value,
            index : this.file_index,
            fileName : '',
            blankImageUrl: '//cliquecities.com/assets/no-image-e3699ae23f866f6cbdf8ba2443ee5c4e.jpg',
            actionType : this.type,
            callback : this.callback
        }
    },
    template: template,
    methods:{
        onFileChanged(){
            let reader;
            reader = new FileReader();
            reader.onload = (function (self, file) {
                return function (e) {
                    self.file.base64 = e.target.result;
                };
            })(this, event.target.files[0]);
            reader.readAsDataURL(event.target.files[0]);
            this.fileName = event.target.files[0].name;
        },
        deleteAction : function(){
            if (this.callback != undefined) {
                this.callback(this.index, function () {
                    if (this.actionType == 'single') {
                        this.file.base64 = '';
                        event.target.files = {}
                        const input = this.$refs.fileupload;
                        input.type = 'text';
                        input.type = 'file';
                    }
                });
            }else{
                if (this.actionType == 'single') {
                    this.file.base64 = '';
                    event.target.files = {}
                    const input = this.$refs.fileupload;
                    input.type = 'text';
                    input.type = 'file';
                }
            }
        }
    },
    computed : {
        sourceImage : function(){
            if (this.file.imageUrl == '' && this.file.base64 == '') {
                return this.blankImageUrl;
            } else if (this.file.base64 != '' ){
                return this.file.base64;
            }else{
                return this.file.imageUrl;
            }
        },
        deleteButton : function(){
            if (this.actionType == 'single') {
                if (this.file.imageUrl == '' && this.file.base64 == '') {
                    return false;
                }else{
                    return true;
                }
            }else{
                return true;
            }
        }
    },
    watch : {
        file: {
            handler: function (newValue) {
                this.file = newValue
            },
            deep: true
        },
        file_index : function(value){
            this.index = value;
        }
    },
    mounted(){
        this.actionType = this.type == undefined ? 'single' : this.type;
    }
})