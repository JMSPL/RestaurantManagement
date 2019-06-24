<template>
    <div class="row">
        <div class="col l8 offset-l2 s12 m6 offset-m3">
            <h3 class="header">Login</h3>
            <div class="card horizontal">
                <div class="card-stacked">
                    <form v-on:submit.prevent="login">
                        <div class="card-content">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="email" v-model="email" type="text">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password" v-model="password" type="password">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action right-align">
                            <button class="btn waves-effect waves-light" name="action" type="submit">Login
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "login",
        data: function(){
            return {
                email: '',
                password: '',
            }
        },
        mounted: function () {
            console.log("Login Mounted!");
        },
        methods: {
            login: function () {
                this.$store.dispatch('login', {
                    email: this.email,
                    password: this.password,
                })
                    .then(res => {
                        this.$router.push('/');
                    })
                    .catch(res => {
                        M.toast({html:res.response.data.message, classes:'red'})
                    })
            }
        },
    }
</script>