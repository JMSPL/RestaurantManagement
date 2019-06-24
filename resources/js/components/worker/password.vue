<template>
    <div class="col s12 m8 offset-m2 l6 offset-l3 card">
        <div class="card-content">
            <span class="card-title">Set your password</span>
            <div class="input-field col s12">
                <input id="password" v-model="password" type="password">
                <label for="password">Password</label>
            </div>
            <div class="input-field col s12" v-if="editing">
                <input id="new_password" v-model="new_password" type="password">
                <label for="new_password">New Password</label>
            </div>
            <div class="input-field col s12" v-if="editing">
                <input id="new_password_confirmation" v-model="new_password_confirmation" type="password">
                <label for="new_password_confirmation">New Password confirmation</label>
            </div>
            <div class="input-field col s12" v-if="editing !== true">
                <input id="password_confirmation" v-model="password_confirmation" type="password">
                <label for="password_confirmation">Password confirmation</label>
            </div>
            <button class="waves-effect waves-light btn-large" style="width: 100%" @click="save">save</button>
            <button v-if="editing" class="waves-effect waves-light btn-large grey" style="width: 100%;margin-top:10px;" @click="cancel">cancel</button>
        </div>
    </div>
</template>

<script>
    const myScripts = require('../../../../public/js/scripts');
    export default {
        props:['editing'],
        data(){
          return {
              password: "",
              password_confirmation: "",
              new_password: "",
              new_password_confirmation: "",
          }
        },
        methods:{
            save(){
                if(this.editing === true){
                    if(this.new_password.length >= 3) {
                        if(this.new_password === this.new_password_confirmation){
                            let request = axios.post('/api/workers/'+this.$store.state.user.id+'/password', {password: this.password});
                            request.then(res => {
                                request = axios.put('/api/workers/'+this.$store.state.user.id+'/password', {password: this.new_password});
                                request.then(res => {
                                    this.$emit('save', res.data);
                                    M.toast({html: res.data.name + " your password is updated!"});
                                });
                                request.catch(res => {
                                    M.toast({html: res.response.data, classes: "red"});
                                })
                            });
                            request.catch(res => {
                                M.toast({html: res.response.data, classes: "red"});
                            })
                        }else{
                            M.toast({html: "Passwords must be the same!", classes: "red"});
                        }
                    }else{
                        M.toast({html: "Password must have at least 3 letters!", classes:"red"});
                    }
                }else{
                    if(this.password.length >= 3) {
                        if (this.password === this.password_confirmation) {
                            let request = axios.post('/api/workers/password/' + this.$route.params.token, {password: this.password});
                            request.then(res => {
                                console.log(res);
                                M.toast({html: res.data.name + " your password is updated!"});
                                this.$router.push('/login');
                            });
                            request.catch(res => {
                                M.toast({html: res.response.data, classes: "red"});
                            })
                        } else {
                            M.toast({html: "Passwords must be the same!", classes: "red"});
                        }
                    }else{
                        M.toast({html: "Password must have at least 3 letters!", classes:"red"});
                    }
                }
            },
            cancel(){
                this.$emit('cancel');
            }
        },
        mounted(){
            myScripts.startInstances();
        }
    }
</script>

<style scoped>

</style>