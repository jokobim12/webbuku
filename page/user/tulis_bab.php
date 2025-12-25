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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Merriweather:wght@300;400;700&family=Patrick+Hand&family=Roboto:wght@300;400;500;700&family=Source+Code+Pro:wght@400;600&display=swap" rel="stylesheet">
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
        
        /* Auto-indentation for paragraphs */
        /* Paragraph spacing */
        .ql-editor p {
            margin-bottom: 0.75em;
            line-height: 1.8;
        }

        @media (max-width: 640px) {
            .ql-editor {
                padding: 1.5rem 1rem;
                font-size: 1.05rem; 
            }
            
            /* Horizontal Scrollable Toolbar on Mobile */
            .ql-toolbar.ql-snow {
                flex-wrap: nowrap !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                justify-content: flex-start !important;
                padding: 12px 16px !important;
                gap: 8px;
                /* Hide scrollbar */
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
            .ql-toolbar.ql-snow::-webkit-scrollbar {
                display: none;
            }

            .ql-toolbar.ql-snow .ql-formats {
                display: flex;
                flex-shrink: 0;
                margin-right: 12px !important;
                margin-bottom: 0 !important;
                padding-right: 12px;
                border-right: 1px solid #f3f4f6;
            }
            .ql-toolbar.ql-snow .ql-formats:last-child {
                border-right: none;
                margin-right: 0 !important;
            }
            
            .ql-snow .ql-picker {
                height: 28px;
            }
        }
        /* Font Families for Quill */
        .ql-font-roboto { font-family: 'Roboto', sans-serif; }
        .ql-font-merriweather { font-family: 'Merriweather', serif; }
        .ql-font-patrick-hand { font-family: 'Patrick Hand', cursive; }
        .ql-font-source-code-pro { font-family: 'Source Code Pro', monospace; }

        /* Dropdown Item Styling */
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="roboto"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="roboto"]::before { content: 'Roboto'; font-family: 'Roboto'; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="merriweather"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="merriweather"]::before { content: 'Merriweather'; font-family: 'Merriweather'; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="patrick-hand"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="patrick-hand"]::before { content: 'Patrick Hand'; font-family: 'Patrick Hand'; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="source-code-pro"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="source-code-pro"]::before { content: 'Code'; font-family: 'Source Code Pro'; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col h-screen overflow-hidden">

    <form action="" method="POST" id="editorForm" class="flex flex-col h-full">
        <input type="hidden" name="content" id="contentInput">
        
        <!-- Top Navigation Bar -->
        <!-- Top Navigation Bar -->
        <header class="bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center justify-between p-4 sticky top-0 z-50 transition-all">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <a href="kelola_cerita.php?id=<?php echo $book_id; ?>" class="p-2 -ml-2 text-gray-400 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <input type="text" name="title" value="<?php echo htmlspecialchars($chap_title); ?>" placeholder="Judul Bab..." class="text-lg font-bold text-gray-900 placeholder-gray-300 focus:outline-none bg-transparent w-full truncate border-none focus:ring-0 p-0" required>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                <button type="submit" name="status" value="draft" onclick="submitContent()" class="hidden sm:block px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                    Draft
                </button>
                <button type="submit" name="status" value="published" onclick="submitContent()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:shadow-emerald-300 transition-all transform active:scale-95">
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
        // Register Fonts
        var Font = Quill.import('formats/font');
        Font.whitelist = ['roboto', 'merriweather', 'patrick-hand', 'source-code-pro'];
        Quill.register(Font, true);

        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'font': ['roboto', 'merriweather', 'patrick-hand', 'source-code-pro'] }],
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
