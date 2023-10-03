
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

const fileSelect = document.getElementById("fileSelect");
const fileElem = document.getElementById("fileElem");

fileSelect.addEventListener("click", (e) => {
  if (fileElem) {
    fileElem.click();
  }
}, false);


// タイムライン取得
react.Post.getTimeline = function(isRefresh) {
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
		url: '/timelines',
        headers: {
            "X-ACCESS-TOKEN" : $("meta[name='access-token']").attr("content"),
            "X-CSRF-TOKEN" : $("meta[name='csrf-token']").attr("content"),
        },
		type: "POST",
		data: {"page" : PostIndex},
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
        if (bottomPoint-300 <= currentPos) {
            loading_flg = true;
            // スクロールが画面末端に到達している時
            react.Post.getTimeline(false);
        }
    }
});

react.Post.addPosts = function(targetElemId, posts) {
    const postsElem = $("#"+targetElemId);

    try {
        for (var i = 0; i < posts.length; i++) {
            react.Post.addPost(postsElem, posts[i]);
        }
    } catch (e) {
        alert("投稿の取得でエラーが発生しました。\n\n" + e.message);
    }
};

react.Post.addPost = function(postsElem, post) {
    let myUserId = $("#user_name").attr("name");

    // ミュートまたはブロックしているorされている相手のやつは表示しない
    if (post.is_ignored) {
        return;
    }

    let uniqueWrapId = Math.random().toString(32).substring(2);

    const postElem = $("#postBaseElemWrap").children("div:first").clone();
    $(postElem).attr("id", uniqueWrapId);

    postElem.find(".status-user_name")[0].innerText = post.user_name;
    postElem.find(".status-screen-name")[0].innerText = post.screen_name;

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
        this.href = "user/" + post.user_name;
    });
    postElem.find(".status-link-post").each(function() {
        this.href = "user/" + post.user_name + "/status/" + post.id;
    });
    postElem.find(".status-link-user").each(function() {
        this.href = "user/" + post.user_name;
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
        if (post.image_path) {
            imageWidthStyle = "width:50%;";
        }

                react.Post.appendPostImage(postElem.find(".status-media")[0], post.image_path, imageWidthStyle);
    }

    $(postElem.find(".status-like-button")[0]).data("postId", post.id);
    $(postElem.find(".status-menu-button")[0]).data("postId", post.id);
    $(postElem.find(".status-menu-button")[0]).data("uniqueWrapId", uniqueWrapId);

    // 自分のポストじゃない場合はメニューを非表示にする
    if (myUserId != post.user_name) {
        $(postElem.find(".editer")[0]).hide();
    }else{
        postElem.find(".status-editer").each(function() {
            this.href = "user/" + post.user_name + "/status/" + post.id + "/editer";
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

r = {}

r.htmlspecialchars = function(str) {
    return (str + '')
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/'/g,'&#039;')
            .replace(/"/g,'&quot;'); 
}