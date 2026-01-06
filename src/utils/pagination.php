<?php

//assumiamo che baseurl contenga già il ? o & se necessario
function createPaginationLinks($currentPage, $totalPages, $baseUrl,$delta=2) {


    if($totalPages <= 1) {
        return '';
    }

    $navigationHtml = '<nav aria-label="Elenco delle pagine"><ul class="pagination">';
    // Link alla pagina precedente, se non siamo alla prima pagina
    if($currentPage>1){
        $navigationHtml .= '<li>
                                <a role="button"href="' . $baseUrl . 'pagina=' . ($currentPage - 1) . '" aria-label="Pagina precedente">
                                <span aria-hidden="true" class="material-symbols-outlined"> arrow_left_alt </span>
                                </a>
                            </li>';
    } 

    //Prima pagina
    if($currentPage>1+$delta){
        $navigationHtml .= '<li>
                                <a role="button" href="' . $baseUrl . 'pagina=1" aria-label="Prima pagina">
                                <span aria-hidden="true" class="first-page"> 1 </span>
                                </a>
                            </li>';
    }


    //elenco delle pagine
    for($i=$currentPage - $delta; $i<=$currentPage + $delta; $i++){
        if($i>0 && $i<=$totalPages){
            if($i==$currentPage){
                $navigationHtml .= '<li class="active" aria-current="page">
                                        <span>' . $i . '</span>
                                    </li>';
            } else {
                $navigationHtml .= '<li>
                                        <a role="button" href="' . $baseUrl . 'pagina=' . $i . '" aria-label="Pagina ' . $i . '">' . $i . '</a>
                                    </li>';
            }
        }

    }



    //ultima pagina
    if($currentPage < $totalPages - $delta){
        $navigationHtml .= '<li>
                                <a role="button" href="' . $baseUrl . 'pagina=' . $totalPages . '" aria-label="Ultima pagina">
                                <span aria-hidden="true" class="last-page"> '.$totalPages.' </span>
                                </a>
                            </li>';
    }





    //link alla pagina successiva, se non siamo all'ultima pagina
    if($currentPage < $totalPages){
        $navigationHtml .= '<li>
                                <a role="button" href="' . $baseUrl . 'pagina=' . ($currentPage + 1) . '" aria-label="Pagina successiva">
                                <span aria-hidden="true" class="material-symbols-outlined"> arrow_right_alt </span>
                                </a>
                            </li>';
    } 
    // else {
    //     $navigationHtml .= '<li class="disabled" aria-disabled="true">
    //                             <span aria-hidden="true" class="material-symbols-outlined"> arrow_right_alt </span>
    //                         </li>';
    // }


    $navigationHtml .='</ul></nav>';
    return $navigationHtml;
}

?>