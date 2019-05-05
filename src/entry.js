const {__} = wp.i18n;
const AmazonCognitoIdentity = require('amazon-cognito-identity-js');

window.addEventListener('DOMContentLoaded', () => {

    class CtaAlisApi {

        constructor(ajaxurl, nonce, username, password) {
            this.nonce = nonce;
            this.ajaxurl = ajaxurl;
            this.username = username;
            this.password = password;
            this.getToken(username, password);
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
    let cta_alis_user_info;

    if(cta_alis_user_info){

        let alisAjaxUrl = cta_alis_user_info.ajax_url;
        let alisNonce = cta_alis_user_info.nonce;
        let alisUsername = cta_alis_user_info.username;
        let alisPassword = cta_alis_user_info.password;
        console.log(alisUsername);

        if(alisAjaxUrl && alisNonce){
            let ctaAlisApi = new CtaAlisApi(alisAjaxUrl, alisNonce, alisUsername, alisPassword);
        }
    }
});
