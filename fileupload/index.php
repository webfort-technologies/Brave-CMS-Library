<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/imageUpload.css">
        <link rel="stylesheet" href="assets/css/multipleImageUpload.css">
        <title>Hello, world!</title>
    </head>
    <body>
        <h1>Hello, world!</h1>
        <div class="container" id="app">
            <div class="row">
                <div class="col-md-12">
                    <!-- <form id="myForm" v-on:submit.prevent="submitForm"> -->
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="text" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" v-model="formData.email">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" v-model="formData.password">
                        </div>
                        <!-- <input type="file" name="myFile" multiple=""  @change="onFileChanged"> -->
                        <div class="d-flex flex-wrap px-2" v-if="formData.images.length != 0">
                            <template v-for="( file,index ) in formData.images">
                                <image-selector v-model="file" :file_index="index" :type="'multiple'" :callback="myFunction" :key="file.order"></image-selector>
                            </template>

                            <!-- <template>
                                <image-selector v-model="formData.images" :type="'single'" :callback="myFunction" :key="formData.images.order"></image-selector>
                            </template> -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button class="btn btn-primary" @click="addFile">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="my-2" v-else>
                            <multiple-image-selector v-model="formData.images"></multiple-image-selector>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" @click="submitForm">Submit</button>
                        </div>
                    <!-- </form> -->
                </div>
                <div class="col-md-6"></div>
            </div>
        </div>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue"></script>
        <script src="assets/js/imageUpload.js"></script>
        <script src="assets/js/imageUploadExtension.js"></script>
        <script>
              var app = new Vue({
                el: '#app',
                data: {
                    message: 'Hello Vue!',
                    selectedFile : [],
                    files : [],
                    formData : {
                        email : '',
                        password : '',
                        images : []
                        // images : {
                        //             order : 0,
                        //             caption: '',
                        //             imageUrl : '',
                        //             base64 : ''
                        //         }       
                    }
                },
                methods: {
                    submitForm(){
                        // const formData = new FormData()
                        // for ( var key in this.formData ) {
                        //     formData.append(key, this.formData[key]);
                        // }

                        $.ajax({
                            url: "fileajax.php",
                            method : 'post',
                            // processData: false,
                            // contentType: false,
                            data : { email : this.formData.email , password : this.formData.password , images : this.formData.images }
                        }).done(function( result ) {
                            console.log( result );
                        });
                    },
                    addFile (){
                        this.formData.images.push({
                                                    order : Math.floor((Math.random() * 100) + 1),
                                                    caption: '',
                                                    imageUrl : '',
                                                    base64 : ''
                                                });
                    },
                    myFunction (index , callback) {
                        console.log(index);
                        this.$delete(this.formData.images,index, 1);
                        return callback();
                    }
                }
            })
        </script>
  </body>
</html>