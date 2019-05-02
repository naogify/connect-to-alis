const {__} = wp.i18n;
const AmazonCognitoIdentity = require('amazon-cognito-identity-js');

window.addEventListener('DOMContentLoaded', () => {

    class CtaAlisApi {

        constructor(ajaxurl,nonce) {
            this.nonce = nonce;
            this.ajaxurl = ajaxurl;

            let publishBtn = document.getElementsByClassName('editor-post-publish-panel__toggle')[0];
            publishBtn.addEventListener("click", ()=>{this.displayPrompt()}, false);
        }

        displayPrompt() {

            let alisUsername = prompt(__('To Publish this post in Alis, enter your Alis Username', 'connect-to-alis'));
            let alisPassword = prompt(__('Also enter Alis Password', 'connect-to-alis'));

            if (alisUsername && alisPassword) {
                alert(__('This post will also share in Alis. Are you OK about that?', 'connect-to-alis'));
                this.getToken(alisUsername, alisPassword);
            }
        }

        getToken(alisUsername, alisPassword) {

            let authenticationData = {
                Username: alisUsername,
                Password: alisPassword,
            };
            let authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);
            let poolData = {
                UserPoolId: 'ap-northeast-1_HNT0fUj4J',
                ClientId: '2gri5iuukve302i4ghclh6p5rg'
            };
            let userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
            let userData = {
                Username: alisUsername,
                Pool: userPool
            };
            let cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
            cognitoUser.authenticateUser(authenticationDetails, {
                onSuccess: (result) => {

                    let idToken = result.idToken.jwtToken;
                    this.sendApiToServer(idToken);

                },

                onFailure: (err) => {
                    alert("Fail to share your post in Alis. Your userid or password is wrong.");
                    console.log(err);
                },

            });
        }

        sendApiToServer(token){

            let data = {
                action: 'get_ajax_data',
                security: this.nonce,
                token: token
            };

            jQuery.post(this.ajaxurl, data, (response) => {
                console.log("Response: " + response);
            });

        }
    }

    let alisAjaxUrl = cta_alis_user_info.ajax_url;
    let alisNonce = cta_alis_user_info.nonce;

    if(alisAjaxUrl && alisNonce){
        let ctaAlisApi = new CtaAlisApi(alisAjaxUrl,alisNonce);

    }
});
