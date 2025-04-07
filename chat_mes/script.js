$(document).ready(function () {
    let currentUserId = 1; // Thay bằng session user ID thực tế

    function loadFriends() {
        $.get("fetch_friends.php", function (data) {
            $("#friend-list").html(data);
        });
    }

    function loadMessages(receiver_id) {
        $.get("fetch_messages.php?receiver_id=" + receiver_id, function (data) {
            let messages = JSON.parse(data);
            let chatBox = $("#chat-messages");
            chatBox.html("");
            messages.forEach(msg => {
                let className = msg.sender_id == currentUserId ? 'sent' : 'received';
                chatBox.append(`<div class="message ${className}"><p>${msg.message}</p></div>`);
            });
            chatBox.scrollTop(chatBox[0].scrollHeight);
        });
    }

    $(document).on("click", ".friend-item", function () {
        let receiver_id = $(this).data("id");
        $("#chat-user").text($(this).text()).data("id", receiver_id);
        loadMessages(receiver_id);

        $("#send-btn").off("click").on("click", function () {
            let message = $("#message").val();
            if (message.trim() !== "") {
                $.post("send_message.php", { message: message, receiver_id: receiver_id }, function (response) {
                    if (response === "success") {
                        $("#message").val("");
                        loadMessages(receiver_id);
                    }
                });
            }
        });
    });

    setInterval(() => {
        let receiver_id = $("#chat-user").data("id");
        if (receiver_id) {
            loadMessages(receiver_id);
        }
    }, 2000);

    loadFriends();
});