// jQuery(window).load(function ($) {
//
//     function cta_alis_user_info() {
//
//         var AWS = require("aws-sdk/dist/aws-sdk");
//         var AmazonCognitoIdentity = require('amazon-cognito-identity-js');
//         var CognitoUserPool = AmazonCognitoIdentity.CognitoUserPool;
//
//         var alisUsername = prompt("To Publish this post in Alis, enter your Alis Username");
//         var alisPassword = prompt("Also enter Alis Password");
//
//         var authenticationData = {
//             Username: alisUsername,
//             Password: alisPassword,
//         };
//
//         var authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);
//         var poolData = {
//             UserPoolId: 'ap-northeast-1_HNT0fUj4J',
//             ClientId: '2gri5iuukve302i4ghclh6p5rg'
//         };
//         var userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
//         var userData = {
//             Username: alisPassword,
//             Pool: userPool
//         };
//
//         var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
//         cognitoUser.authenticateUser(authenticationDetails, {
//             onSuccess: function (result) {
//                 var accessToken = result.getAccessToken().getJwtToken();
//
//                 /* Use the idToken for Logins Map when Federating User Pools with identity pools or when passing through an Authorization Header to an API Gateway Authorizer */
//                 var idToken = result.idToken.jwtToken;
//
//                 console.log(idToken);
//
//                 var data = {
//                     'action': 'getToken',
//                     'security': cta_alis_user_info.nonce,
//                     'idToken': idToken
//                 };
//
//                 // if (idToken) {
//                 //     // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
//                 //     jQuery.post(ajaxurl, data, function (response) {
//                 //         // alert('Got this from the server: ' + response);
//                 //     });
//                 // }
//             },
//
//             onFailure: function (err) {
//                 console.log(err);
//                 alert(err);
//             },
//
//         });
//
//
//     }
//
//     var publishBtn = document.getElementsByClassName('editor-post-publish-panel__toggle')[0];
//     publishBtn.addEventListener("click", cta_alis_user_info, false);
//
// });

var AWS = require("aws-sdk/dist/aws-sdk");
var AmazonCognitoIdentity = require('amazon-cognito-identity-js');
var CognitoUserPool = AmazonCognitoIdentity.CognitoUserPool;

var authenticationData = {
    Username : '',
    Password : '',
};
var authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);
var poolData = { UserPoolId : 'ap-northeast-1_HNT0fUj4J',
    ClientId : '2gri5iuukve302i4ghclh6p5rg'
};
var userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
var userData = {
    Username : '',
    Pool : userPool
};
var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
cognitoUser.authenticateUser(authenticationDetails, {
    onSuccess: function (result) {
        var accessToken = result.getAccessToken().getJwtToken();

        /* Use the idToken for Logins Map when Federating User Pools with identity pools or when passing through an Authorization Header to an API Gateway Authorizer*/
        var idToken = result.idToken.jwtToken;
        console.log(idToken);
    },

    onFailure: function(err) {
        alert(err);
    },

});
