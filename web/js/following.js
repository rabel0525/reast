
// タイッツー
var react = {};
// APIキー
react.ApiKey = "mr5cs4KixwyqsnGcTVU5qsTYGXbvsgmZgMk799SaC2gbFigZ";
// アクセストークン
react.AccessToken = null;
//ReastID
react.reactId = null;
// メディア自動表示
react.AutoMediaShow = 1;

var PostIndex = 1;
var status_empty = false;

// ポスト関係
react.Post = {};
// 次の取得位置
react.Post.nextPostId = null;
react.Post.isLoading = false;
react.Post.isTaiitsuing = false;
// 表示済みID
react.Post.shownRTIDs = [];
// デフォルト表示テキスト
react.Post.defaultText = '';

let UserId = $("#u_id").attr("name");

$(function() {
    $('.button').click(function(){
    $(this).toggleClass('active');
    $('.sidenav').toggleClass('view_menu');
    });
  });
  $(function() {  
    $('.button').click(function () {　  
      if ($(this).hasClass("active")) {  
        $("html").addClass("no-scroll");  
      } else {                              
        $("html").removeClass("no-scroll");
      }
    });
  });

// タイムライン取得
react.Post.getfollowing = function(isRefresh) {
    // 取得中なら何もしない
    if (react.Post.isLoading) {
        return;
    }

    // 一旦消す
    if (isRefresh) {
        react.Post.nextPostId = null;
        react.Post.shownRTIDs = [];
        $("#posts").empty();
    }



    if(!status_empty){
    // タイムライン取得
	$.ajax({
		url: '/get_following_user',
		type: "POST",
		data: {"page" : PostIndex, "user_id" : UserId},
		dataType: "json",
		timespan: 5000,

        // 成功時
		}).done(function(data, status, xhr) {
            if (xhr.status != 200) {
                alert("タイムラインの取得に失敗しました。(" + xhr.status + ")");
                return;
            }
            if($.trim(data)==''){
                status_empty = true;
           }
           if(PostIndex == '1' && status_empty == true){
            let element = document.getElementById('loaded');
            element.innerText = 'まだ、誰もフォローしていないようです...';  
        }
                    // 投稿を追加
                    react.Post.addPosts("posts", data);
                    PostIndex += 1
                

        // 失敗時
        }).fail(function(xhr, status, errorThrown) {
            alert("タイムラインの取得に失敗しました。(" + xhr.status + ")");

		// 必ず実行
		}).always(function() {
            // ローディング非表示
            $("#refreshButton").show();
            $("#loadButton").show();
            $("#loadingAnimation").hide();
            react.Post.isLoading = false;
        });
    }else{
        console.log('All of status has already loaded');    
        $("#loadingAnimation").hide();
        react.Post.isLoading = false; 
        let element = document.getElementById('loaded');
        element.innerText = '全てのフォロー中のユーザーが読み込まれました。';  
    }
};

$(window).scroll(function() {
    // 読込処理中ではないか判定
    if (!react.Post.isLoading) {
        // 画面に見えている最上部の座標が分かる = [bodyの高さ] - [windowの高さ]
        var bottomPoint = document.body.clientHeight - window.innerHeight;
        // スクロール量を取得
        var currentPos = window.pageYOffset;
        // スクロール量が最下部の位置を過ぎたか判定
        if (bottomPoint-100 <= currentPos) {
            // スクロールが画面末端に到達している時
            react.Post.getfollowing(false);
            react.Post.isLoading = true;
        }
    }
});



react.Post.addPosts = function(targetElemId, posts) {
    const postsElem = $("#"+targetElemId);

    try {
        for (var i = 0; i < posts.length; i++) {
            react.Post.addPost(postsElem, posts[i]);
        }
        react.Post.isLoading = false;
    } catch (e) {
        alert("投稿の取得でエラーが発生しました。\n\n" + e.message);
    }
};

react.Post.addPost = function(postsElem, post) {

    // ミュートまたはブロックしているorされている相手のやつは表示しない
    if (post.is_ignored) {
        return;
    }

    let uniqueWrapId = Math.random().toString(32).substring(2);

    const postElem = $("#postBaseElemWrap").children("div:first").clone();

    $(postElem).attr("id", uniqueWrapId);

    postElem.find("#user_id")[0].innerText = post.user_name;
    postElem.find(".p_screen_name")[0].innerText = post.screen_name;
    postElem.find(".introduce")[0].innerText = post.introduce;
    postElem.find(".followings")[0].innerText = post.following;
    postElem.find(".follower")[0].innerText = post.followers;
    postElem.find(".posts_count")[0].innerText = post.all_posts;

    postElem.find(".user_link").each(function() {
        this.href = "/user/" + post.user_name;
    });

    // プロフィール画像があればセット
    if (post.profile_image) {
        $(postElem.find(".user_profileimage")[0]).prop("src", "/profileimages/" + post.profile_image);
    } else {
        $(postElem.find(".user_profileimage")[0]).prop("src", '/profileimages/default.png');
    }

    
    react.Post.shownRTIDs.push(post.id);
    postsElem.append(postElem);

};

r = {}

r.htmlspecialchars = function(str) {
    return (str + '')
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/'/g,'&#039;')
            .replace(/"/g,'&quot;'); 
}