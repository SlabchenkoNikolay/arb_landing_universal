document.addEventListener("DOMContentLoaded", function() {
    // Получаем все комментарии
    var comments = document.querySelectorAll('.fb-comments-wrapper');
    if (!comments.length) return;
    
    // Сначала скрываем все
    comments.forEach(function(comment) {
        comment.style.display = 'none';
        comment.style.opacity = 0;
    });
    
    // Функция для постепенного показа комментария
    function showComment(index) {
        if (index >= comments.length) return;
        
        var comment = comments[index];
        comment.style.display = 'block';
        
        // Анимация появления
        setTimeout(function() {
            comment.style.opacity = 1;
        }, 50);
        
        // Запланировать показ следующего комментария
        setTimeout(function() {
            showComment(index + 1);
        }, 3500); // 5 секунд между появлениями
    }
    
    // Начинаем показывать комментарии
    showComment(0);
});