
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

const btn = $(".image_click");
btn.click(function () {
    if (fileElem) {
        fileElem.click();
    }
});
const btn2 = $("#sele2");
btn2.click(function () {
    if (fileElem2) {
        fileElem2.click();
    }
});


$(document).on("change","#fileElem",function(){
    let elem = this                                             //操作された要素を取得
    let fileReader = new FileReader();                          //ファイルを読み取るオブジェクトを生成
    fileReader.readAsDataURL(elem.files[0]);                    //ファイルを読み取る
    fileReader.onload = (function () {                          //ファイル読み取りが完了したら
        $('.p_user_image').children('img').attr('src', fileReader.result);
    });
})
$(document).on("change","#fileElem2",function(){
    let elem = this                                             //操作された要素を取得
    let fileReader = new FileReader();                          //ファイルを読み取るオブジェクトを生成
    fileReader.readAsDataURL(elem.files[0]);                    //ファイルを読み取る
    fileReader.onload = (function () {                          //ファイル読み取りが完了したら
        $('body').css('background-image', 'url('+ fileReader.result +')');
    });
})
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
react.Post.getUserPosts = function(isRefresh) {
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

    // ローディング表示
    $("#refreshButton").hide();
    $("#loadButton").hide();
    $("#loadingAnimation").show();
    react.Post.isLoading = true;

    if(!status_empty){
    // タイムライン取得
	$.ajax({
		url: '/user_posts',
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
           if(PostIndex == '1' && $.trim(data)==''){
            let element = document.getElementById('loaded');
            element.innerText = 'まだ、何も投稿していないようです...';  
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
        element.innerText = '全ての投稿が読み込まれました。'; 
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
            react.Post.getUserPosts(false);
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
    let myUserId = $("#user_id").text();

    // ミュートまたはブロックしているorされている相手のやつは表示しない
    if (post.is_ignored) {
        return;
    }

    let uniqueWrapId = Math.random().toString(32).substring(2);

    const postElem = $("#postBaseElemWrap").children("div:first").clone();
    $(postElem).attr("id", uniqueWrapId);

    postElem.find(".status-user_name")[0].innerText = post.user_name;
    postElem.find(".status-screen-name")[0].innerText = post.screen_name;

    postElem.find(".status-like-button-value")[0].innerText = post.likes;
    
    // 本文を入れていく
    postElem.find(".status-content")[0].innerText = post.body;
    $postContentElem = $(postElem.find(".status-content")[0]);
    var postContentElemHtml = $postContentElem.html();
    postContentElemHtml = postContentElemHtml.replace(/\t/ig, " ");
    // URLを抽出しておく
    let regUrlFormat = /((https?):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    let matchUrls = postContentElemHtml.match(regUrlFormat);
    // 一旦URL部分を別の制御文字に置き換えておく
    postContentElemHtml = postContentElemHtml.replace(regUrlFormat, "\t");
    // メンションをリンク化する
    let regMentionFormat = /(@)([0-9a-zA-Z_]{4,15})/ig;
    postContentElemHtml = postContentElemHtml.replace(regMentionFormat, "<a href='user/$2' target='_blank'>$1$2</a>");
    // 本文のURLを戻していく
    if (matchUrls != null) {
        for (var i = 0; i < matchUrls.length; i++) {
            postContentElemHtml = postContentElemHtml.replace("\t", "<a href='"+matchUrls[i]+"' target='_blank'>"+matchUrls[i]+"</a>");
        }
    }
    // 本文を格納し直す
    $postContentElem.html(postContentElemHtml);

    // 時刻は整形する
    let createdAt = new Date(post.created_at.replace(/-/g,"/"));
    postElem.find(".status-time")[0].innerText = createdAt.toLocaleString();

    postElem.find(".status-screen-name").each(function() {
        this.href = "../user/" + post.user_name;
    });
    postElem.find(".status-link-post").each(function() {
        this.href = "../user/" + post.user_name + "/status/" + post.id;
    });
    postElem.find(".status-link-user").each(function() {
        this.href = "../user/" + post.user_name;
    });

    // プロフィール画像があればセット
    if (post.profile_image) {
        $(postElem.find(".status-image")[0]).prop("src", "/profileimages/" + post.profile_image);
    } else {
        $(postElem.find(".status-image")[0]).prop("src", '/profileimages/default.png');
    }

    // 添付画像があればセット
    if (!post.image_path) {
        $(postElem.find(".status-media")[0]).hide();
    } else {
        imageWidthStyle = "width:34%; margin-right:1%; aspect-ratio:4/5;";
        
        imageWidthStyle = "aspect-ratio: 16/9;";
        // 配列を定義
            let csvArray = [];
            
            // 改行ごとに配列化
            let lines = post.image_path.split(/\r\n|\n/);

            console.log(lines.length);

    if(lines.length > 1){
        imageWidthStyle = "width: 46%";
        for (let i = 0; i < lines.length; ++i) {
            react.Post.appendPostImage(postElem.find(".status-media")[0], lines[i], imageWidthStyle);
            console.log(csvArray[i]);
         }
    }else{
        react.Post.appendPostImage(postElem.find(".status-media")[0], post.image_path, imageWidthStyle);
    }
    }

    $(postElem.find(".status-like-button")[0]).data("postId", post.id);
    $(postElem.find(".status-menu-button")[0]).data("postId", post.id);
    $(postElem.find(".status-menu-button")[0]).data("uniqueWrapId", uniqueWrapId);

    if (post.is_liked) {
        $(postElem.find(".status-like-button")[0]).removeClass("status-button-off");
    }

    // 自分のポストじゃない場合はメニューを非表示にする
    if (myUserId != post.user_name) {
        $(postElem.find(".editer")[0]).hide();
    }else{
        postElem.find(".status-editer").each(function() {
            this.href = "/user/" + post.user_name + "/status/" + post.id + "/editer";
        });
    }
    if (post.edited == 'FALSE') {
        $(postElem.find(".edited")[0]).hide();
    }
    
    react.Post.shownRTIDs.push(post.id);
    postsElem.append(postElem);

};

react.Post.appendPostImage = function(paramElem, url, imageStyle) {
    let imageElem = $("<img>");
    imageElem.prop("src", "/images/" + url);
    imageElem.addClass("status-media-image");
    imageElem.prop("style", imageStyle);
    
    let imageAElem = $("<a target='_blank'>");
    imageAElem.prop("href", "/images/" + url);
    imageAElem.append(imageElem);
    $(paramElem).append(imageAElem);
};

// LIKE実施
react.Post.like = function(elem) {
    // 対象のタイーツID取得
    $elem = $(elem);
    let postId = $elem.data("postId");
    // 現在のLIKE状態取得
    let isLiked = !$elem.hasClass("status-button-off");
    let likeType = isLiked ? "del" : "up";
    $elem.toggleClass("status-button-off");
    // 件数更新
    let countElem = $elem.find(".status-like-button-value")[0];
    $nowCount = Number(countElem.innerText);
    if (isLiked) {
        countElem.innerText = $nowCount - 1;
    } else {
        countElem.innerText = $nowCount + 1;
    }

    // LIKE
	$.ajax({
		url:"/status/likes",
		type: "POST",
		data: {"status_id" : postId,
               "likeType" : likeType},
		dataType: "json",
		timespan: 20000,

        // 成功時
		}).done(function(data, status, xhr) {

        // 失敗時
        }).fail(function(xhr, status, errorThrown) {
            $elem.toggleClass("status-button-off");

		// 必ず実行
		}).always(function() {
        });
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