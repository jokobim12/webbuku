<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($book = mysqli_fetch_assoc($result)) {
?>
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition overflow-hidden">

        <!-- THUMBNAIL (SQUARE) -->
        <div class="p-1 bg-gray-100 dark:bg-gray-700">
            <a href="detail.php?id=<?php echo $book['id']; ?>" 
               class="block aspect-square bg-white dark:bg-gray-800 overflow-hidden shadow">
                
                <?php if (!empty($book['cover_image'])): ?>
                    <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>"
                         alt="Cover"
                         class="w-full h-full object-cover object-center">
                <?php else: ?>
                    <div class="w-full h-full flex flex-col items-center justify-center text-emerald-500">
                        <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
                        </svg>
                        <span class="text-xs font-semibold">No Cover</span>
                    </div>
                <?php endif; ?>
            </a>
        </div>

        <!-- CONTENT -->
        <div class="px-3 pb-3 space-y-1">
            <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">
                <?php echo htmlspecialchars($book['genre']); ?>
            </span>

            <h3 class="text-sm font-semibold leading-snug line-clamp-2">
                <?php echo htmlspecialchars($book['title']); ?>
            </h3>

            <p class="text-[11px] text-gray-500 line-clamp-1">
                <?php echo htmlspecialchars($book['author_name']); ?>
            </p>

            <div class="pt-2 flex items-center justify-between">
                <span class="text-[11px] text-gray-400">👁 <?php echo (int)$book['views']; ?></span>

                <!-- BUTTON -->
                <a href="detail.php?id=<?php echo $book['id']; ?>"
                   class="px-3 py-1.5 rounded-lg text-[11px] font-semibold
                          bg-emerald-600 text-white hover:bg-emerald-700 transition">
                    Baca
                </a>
            </div>
        </div>
    </div>
<?php
    }
} else {
    echo '<div class="col-span-full text-center py-16 text-gray-500">';
    echo 'Tidak ada buku.';
    echo '</div>';
}
?>
</div>
