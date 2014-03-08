""""""""""""""""""""""""
set ts=4
set fenc=utf-8
set sw=4
set fileencoding=cp936
set fileencodings=gbk,gb2312,utf-8,cp936
set encoding=cp936
set autoindent
set showcmd
set nocompatible
set smartindent
set showmatch
set t_Co=8
map <C-J> :set filetype=cpp<CR>
set hls
set incsearch

set tags+=tags;
if has("terminfo")
	set t_Co=8
	set t_Sf=^[[3%p1%dm
	set t_Sb=^[[4%p1%dm
else
	set t_Co=8
	set t_Sf=^[[3%dm
	set t_Sb=^[[4%dm
endif

"set wrap
syntax enable
syntax on
highlight Comment ctermfg=cyan
highlight Macro   ctermfg=gray
highlight Include ctermfg=magenta

fun! ToggleFold()
    if foldlevel('.') == 0
        normal! l
    else
        if foldclosed('.') < 0
            . foldclose!
        else
            . foldopen!
        endif
    endif
    " Clear
    " status
    " line 
    echo
endfun

noremap <space> :call ToggleFold()<CR>
function FoldBrace()
    if getline(v:lnum+1)[0] == '{'
        return 1
    endif
    if getline(v:lnum) =~ '{'
        return 1
    endif
    if getline(v:lnum)[0] =~ '}'
        return '<1'
    endif
    return -1
endfunction

if has("autocmd")
    filetype plugin indent on

    autocmd FileType text setlocal textwidth=78
    autocmd FileType text set nocindent
    autocmd FileType html set formatoptions+=tl
    autocmd FileType css  set smartindent
    autocmd FileType html,css set noexpandtab tabstop=2
    autocmd FileType c,cpp,slang,esqlc set cindent

    "augroup vimrcEx
    "au!
    "autocmd FileType text setlocal textwidth=78
    "autocmd BufReadPost *
    "   \ if line("'\"") > 0 && line("'\"") <= line("$") |
    "   \   exe "normal g`\"" |
    "   \ endif
    "augroup END

    autocmd BufReadPost *
                \ if line("'\"") > 0 && line("'\"") <= line("$") |
                \   exe "normal g`\"" |
                \ endif
    autocmd BufEnter * :lcd %:p:h

    autocmd BufNewFile *.c,*.ec,*.C,*.cpp,*.php,*.pc,*.cc   0r~/.vim/templates/cpp.cpp

    autocmd BufReadPost *.h,*.hh,*.c,*.ec,*.cpp,*.hpp,*.php,*.pc,*.cc set foldexpr=FoldBrace()
    autocmd BufReadPost *.h,*.hh,*.c,*.ec,*.cpp,*.hpp,*.php,*.pc,*.cc set foldmethod=expr
    autocmd BufReadPost *.h,*.hh,*.c,*.ec,*.cpp,*.hpp,*.php,*.pc,*.cc set foldenable
endif

map <C-K> gt
map <C-H> gT
map <Tab> gt
