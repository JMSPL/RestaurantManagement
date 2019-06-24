<template>
    <form action="">
        <div class="row">
            <div class="input-field col s4 offset-s2">
                <input id="name" placeholder="Name"  type="text" v-model="item.name">
                <label for="name" class="active">Title</label>
            </div>
            <div class="input-field col s4">
                <input id="price" placeholder="Price"  type="number" min="0" step="0.01" v-model="item.price">
                <label for="price" class="active">Price (€)</label>
            </div>
            <div class="input-field col s8 offset-s2">
                <textarea id="description" class="materialize-textarea" style="min-height: 100px;" v-model="item.description"></textarea>
                <label for="description" class="active">Description</label>
            </div>
            <div class="file-field input-field col s4 offset-s2">
                <div class="btn">
                    <span><i class="material-icons">add_a_photo</i></span>
                    <input type="file" name="photo_url" ref="file" @change="handleFileUpload">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Update photo">
                </div>
            </div>
            <div class="input-field col s4">
                <select name="type" id="select" v-model="item.type">
                    <option value="" disabled :class="{item}"> -- Select Type -- </option>
                    <option value="drink" :class="{ active: item.type === 'drink' }">Drink</option>
                    <option value="dish" :class="{ active: item.type === 'dish' }">Dish</option>
                </select>
                <label>Item type</label>
            </div>
        </div>
        <div class="row">
            <div class="col offset-s2 s8" style="text-align: right">
                <a class="waves-effect waves-light btn grey" @click="cancelItem">Cancel</a>
                <a class="waves-effect waves-light btn" @click="saveItem">Save</a>
            </div>
        </div>
    </form>
</template>

<script>
    const myScripts = require('../../../../public/js/scripts');

    export default {
        props:['item', 'operation'],
        data: function(){
            return {
                file: '',
                errors: [],
            }
        },
        mounted: function(){
            myScripts.startInstances();
        },
        methods: {
            cancelItem: function(){
                this.$emit('cancel-item');
            },
            saveItem: function(){
                let user = {};
                if(this.operation === "edit"){
                    axios.put('/api/menus/'+this.item.id, this.item)
                        .then(res => {
                            if(this.file !== ""){
                                let formData = new FormData();
                                formData.append('file', this.file);

                                axios.post('/api/menus/'+this.item.id, formData,{
                                    headers: {
                                        'Content-type': 'multipart/form-data'
                                    },
                                })
                                    .then(res=>{
                                        M.toast({html: '\"'+this.item.name+'\" updated!'})
                                        this.$emit('save-item');
                                    })
                                    .catch(res => {
                                        this.errors = res.errors;
                                    });
                            }else{
                                this.$emit('save-item');
                            }
                        })
                        .catch(res => {
                            this.errors = res.errors;
                        });
                }else if(this.operation === "create"){

                    let formData = new FormData();
                    formData.append('file', this.file);

                    axios.post('/api/menus/photo', formData, {
                        headers: {
                            'Content-type': 'multipart/form-data'
                        },
                    })
                        .then(res => {
                            this.item.photo_url = res.data;
                            axios.post('/api/menus', this.item)
                                .then(res=>{
                                    M.toast({html: '\"'+this.item.name+'\" created!'});
                                    this.$emit('save-item');
                                })
                                .catch(res => {
                                    this.errors = res.errors;
                                });
                        })
                        .catch(res => {
                            this.errors = res.errors;
                        });
                }
            },
            handleFileUpload: function(){
                this.file = this.$refs.file.files[0];
            }
        }
    }
</script>