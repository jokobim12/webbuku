        <div class="grid grid-cols-3 lg:grid-cols-5 gap-3 md:gap-8 min-h-[400px]">
            <!-- Book Items (From Database) -->
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($book = mysqli_fetch_assoc($result)) {
            ?>
            <div class="group bg-white dark:bg-gray-800 rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="relative h-36 md:h-72 bg-gray-200 overflow-hidden">
                    <?php if (!empty($book['cover_image'])): ?>
                        <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400">
                             <svg class="w-8 md:w-12 h-8 md:h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                             <span class="font-bold text-[10px] md:text-base">No Cover</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute top-1.5 right-1.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[10px] md:text-xs font-bold text-gray-700 dark:text-gray-200 shadow-sm truncate max-w-[90%]">
                        <?php echo htmlspecialchars($book['genre']); ?>
                    </div>
                </div>
                <div class="p-2.5 md:p-5">
                    <h3 class="text-[11px] md:text-lg font-bold text-gray-900 dark:text-white mb-0.5 line-clamp-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 mb-2 md:mb-4 line-clamp-1">Oleh <span class="text-gray-700 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($book['author_name']); ?></span></p>
                    
                    <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-2 md:pt-4">
                        <span class="text-[10px] md:text-xs text-gray-400 flex items-center gap-0.5">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <?php echo $book['views']; ?>
                        </span>
                        <a href="detail.php?id=<?php echo $book['id']; ?>" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold text-[10px] md:text-sm">Baca</a>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo '<div class="col-span-full text-center py-16">';
                echo '<div class="text-6xl mb-4">🔍</div>';
                echo '<h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak ditemukan cerita</h3>';
                echo '<p class="text-gray-500 dark:text-gray-400">Coba kata kunci lain atau ubah filter genre.</p>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): 
            $query_params = $_GET;
            unset($query_params['page']);
            unset($query_params['ajax']); // Don't include ajax param in links
            $base_url = '?' . http_build_query($query_params);
            $connector = empty($query_params) ? '' : '&';
        ?>
        <div class="mt-16 flex justify-center gap-2 flex-wrap pagination-container">
            <!-- Prev Button -->
            <?php if ($page > 1): ?>
                <a href="<?php echo $base_url . $connector . 'page=' . ($page - 1); ?>" class="pagination-link w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition-colors">←</a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php 
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            if ($start_page > 1) {
                echo '<a href="'.$base_url . $connector . 'page=1" class="pagination-link w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition-colors">1</a>';
                if ($start_page > 2) echo '<span class="flex items-end px-2 text-gray-400">...</span>';
            }

            for ($i = $start_page; $i <= $end_page; $i++): 
                $activeClass = ($i == $page) ? 'bg-emerald-600 text-white font-bold shadow-lg border-emerald-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700';
            ?>
                <a href="<?php echo $base_url . $connector . 'page=' . $i; ?>" class="pagination-link w-10 h-10 flex items-center justify-center rounded-full border transition-colors <?php echo $activeClass; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php 
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) echo '<span class="flex items-end px-2 text-gray-400">...</span>';
                echo '<a href="'.$base_url . $connector . 'page='.$total_pages.'" class="pagination-link w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition-colors">'.$total_pages.'</a>';
            }
            ?>

            <!-- Next Button -->
            <?php if ($page < $total_pages): ?>
                <a href="<?php echo $base_url . $connector . 'page=' . ($page + 1); ?>" class="pagination-link w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition-colors">→</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
