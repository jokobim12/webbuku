<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['book_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../database/koneksi.php';

$book_id = mysqli_real_escape_string($koneksi, $_GET['book_id']);
$user_id = $_SESSION['user_id'];
$chapter_id = isset($_GET['chapter_id']) ? mysqli_real_escape_string($koneksi, $_GET['chapter_id']) : null;

// Verify Book Access
$query_book = "SELECT * FROM books WHERE id = '$book_id' AND user_id = '$user_id'";
$book = mysqli_fetch_assoc(mysqli_query($koneksi, $query_book));
if (!$book) exit("Akses ditolak.");

// Initialize variables
$chap_title = '';
$chap_content = '';

// If editing existing chapter
if ($chapter_id) {
    $query_chap = "SELECT * FROM chapters WHERE id = '$chapter_id' AND book_id = '$book_id'";
    $chap = mysqli_fetch_assoc(mysqli_query($koneksi, $query_chap));
    if ($chap) {
        $chap_title = $chap['title'];
        $chap_content = $chap['content'];
    }
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $content = mysqli_real_escape_string($koneksi, $_POST['content']);
    $status = $_POST['status'];

    if ($chapter_id) {
        // Update
        $query = "UPDATE chapters SET title='$title', content='$content', status='$status' WHERE id='$chapter_id'";
    } else {
        // Insert
        $query = "INSERT INTO chapters (book_id, title, content, status) VALUES ('$book_id', '$title', '$content', '$status')";
    }

    if (mysqli_query($koneksi, $query)) {
        header("Location: kelola_cerita.php?id=$book_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menulis: <?php echo htmlspecialchars($book['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .ql-editor {
            min-height: 70vh;
            font-family: 'Georgia', serif;
            font-size: 1.125rem;
            line-height: 1.8;
            color: #374151;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        .ql-toolbar.ql-snow {
            border: none;
            border-bottom: 1px solid #e5e7eb;
            background: rgba(255,255,255,0.9);
            position: sticky;
            top: 0;
            z-index: 10;
            text-align: center;
        }
        .ql-container.ql-snow {
            border: none;
        }
        /* Custom Toolbar Styling */
        .ql-toolbar.ql-snow {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Center items on mobile */
            padding: 8px !important;
        }
        .ql-toolbar.ql-snow .ql-formats {
            margin-right: 8px !important;
        }
        
        @media (max-width: 640px) {
            .ql-editor {
                padding: 1rem;
                font-size: 1rem; /* Slightly smaller text on mobile */
            }
            /* Condense toolbar on mobile */
            .ql-toolbar.ql-snow .ql-formats {
                margin-right: 4px !important;
                margin-bottom: 4px !important; /* Add space when wrapping */
            }
            .ql-snow .ql-picker {
                height: 24px; /* Compact dropwdown */
            }
            .ql-snow .ql-picker-label {
                padding-left: 4px;
            }
            button.ql-bold, button.ql-italic, button.ql-underline, button.ql-strike, button.ql-blockquote {
                padding: 2px !important;
                width: 24px !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col h-screen overflow-hidden">

    <form action="" method="POST" id="editorForm" class="flex flex-col h-full">
        <input type="hidden" name="content" id="contentInput">
        
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-gray-200 flex flex-col items-start gap-4 p-4 md:flex-row md:items-center md:justify-between md:h-16 md:px-6 md:py-0 z-20 flex-shrink-0">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="kelola_cerita.php?id=<?php echo $book_id; ?>" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="flex flex-col w-full">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($chap_title); ?>" placeholder="Judul Bab..." class="text-lg font-bold text-gray-800 placeholder-gray-300 focus:outline-none bg-transparent w-full md:w-96" required>
                </div>
            </div>

            <div class="flex items-center justify-end w-full md:w-auto gap-2 border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
                <button type="submit" name="status" value="draft" onclick="submitContent()" class="px-4 py-2 text-xs font-medium text-gray-500 hover:text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    Simpan Draft
                </button>
                <button type="submit" name="status" value="published" onclick="submitContent()" class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-sm transition-colors">
                    Publikasikan
                </button>
            </div>
        </header>

        <!-- Editor Area -->
        <div class="flex-1 overflow-y-auto bg-white relative">
            <div id="editor-container"><?php echo $chap_content; ?></div>
        </div>

        <!-- Footer Info -->
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-2 text-xs text-gray-400 flex justify-between flex-shrink-0">
            <span id="word-count">0 words</span>
            <span><?php echo htmlspecialchars($book['title']); ?></span>
        </div>

    </form>

    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, false] }],
                    ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            },
            placeholder: 'Mulai menulis ceritamu di sini...'
        });

        // Word Count
        quill.on('text-change', function() {
            var text = quill.getText();
            var wordCount = text.trim().length === 0 ? 0 : text.trim().split(/\s+/).length;
            document.getElementById('word-count').innerText = wordCount + ' words';
        });

        function submitContent() {
            document.getElementById('contentInput').value = quill.root.innerHTML;
        }
    </script>
</body>
</html>
