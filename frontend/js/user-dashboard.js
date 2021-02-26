$(document).ready(function() {
    var str = "";
    for(var i = 0; i < trx_all.length; i ++){
        if(trx_all[i].user_id == user_id)
        {
            str += "<tr>";
            str += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
            str += trx_all[i].trx;
            str += "</a></span></div></td>";

            str += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
            var today = new Date();
            var created_day = new Date(trx_all[i].created_at);
            var diff_time = today.getTime() - created_day.getTime();
            var diff_day = diff_time / (1000 * 3600 * 24);
            diff_day = parseInt(diff_day);
            str += diff_day;
            str += " days ago";
            str += "</span></div></td>";

            str += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
            if(trx_all[i].type > 0)
                trx_amount = parseFloat(trx_all[i].getamo);
            else
                trx_amount = parseFloat(trx_all[i].amountpaid);
            str += trx_amount.toFixed(basic.decimal);     
            str += "<span class='tb-sub text-primary'>";
            str += 'SMC';
            str += '</span>';
            str += "</span></div></td>";
            
            str += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
            if(trx_all[i].type > 0)
                trx_amount = parseFloat(trx_all[i].amountpaid);
            else
                trx_amount = parseFloat(trx_all[i].getamo);
            str += trx_amount.toFixed(basic.decimal);
            str += "<span class='tb-sub text-primary'>";
            str += trx_all[i].remark;
            str += "</span>";
            str += "</sapn></div></td>";

            if(trx_all[i].status == 2){
                str += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                str += "Success";
            }
            else if(trx_all[i].status == 1){
                str += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                str += "Pending";
            }
            else if(trx_all[i].status == -2){
                str += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                str += "Declined";
            }   
                
            str += "</span></div></td>";
            str += "</tr>";
        }
    }
    $("#table-id-body").append(str);
    $("#table-id").DataTable();
    $('#withdraw-total').on('click', function () {
        $("#table-id").DataTable().destroy();
        $("#table-id-body").empty();
        var str_table_body = "";
        for(var i = 0; i < trx_all.length; i ++){
            if(trx_all[i].user_id == user_id)
            {
                if(trx_all[i].type == 0)
                {
                    str_table_body += "<tr>";
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
                    str_table_body += trx_all[i].trx;
                    str_table_body += "</a></span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
                    var today = new Date();
                    var created_day = new Date(trx_all[i].created_at);
                    var diff_time = today.getTime() - created_day.getTime();
                    var diff_day = diff_time / (1000 * 3600 * 24);
                    diff_day = parseInt(diff_day);
                    str_table_body += diff_day;
                    str_table_body += " days ago";
                    str_table_body += "</span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].getamo);
                    else
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    str_table_body += trx_amount.toFixed(basic.decimal);     
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += 'SMC';
                    str_table_body += '</span>';
                    str_table_body += "</span></div></td>";
                    
                    str_table_body += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    else
                        trx_amount = parseFloat(trx_all[i].getamo);
                    str_table_body += trx_amount.toFixed(basic.decimal);
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += trx_all[i].remark;
                    str_table_body += "</span>";
                    str_table_body += "</sapn></div></td>";

                    if(trx_all[i].status == 2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                        str_table_body += "Success";
                    }
                    else if(trx_all[i].status == 1){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                        str_table_body += "Pending";
                    }
                    else if(trx_all[i].status == -2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                        str_table_body += "Declined";
                    }   
                        
                    str_table_body += "</span></div></td>";
                    str_table_body += "</tr>";
                }
            }
        }
        $("#table-id-body").append(str_table_body);
        $("#table-id").DataTable();
    });

    $('#withdraw-pending').on('click', function () {
        $("#table-id").DataTable().destroy();
        $("#table-id-body").empty();
        var str_table_body = "";
        for(var i = 0; i < trx_all.length; i ++){
            if(trx_all[i].user_id == user_id)
            {
                if(trx_all[i].type == 0 && trx_all[i].status == 1)
                {
                    str_table_body += "<tr>";
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
                    str_table_body += trx_all[i].trx;
                    str_table_body += "</a></span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
                    var today = new Date();
                    var created_day = new Date(trx_all[i].created_at);
                    var diff_time = today.getTime() - created_day.getTime();
                    var diff_day = diff_time / (1000 * 3600 * 24);
                    diff_day = parseInt(diff_day);
                    str_table_body += diff_day;
                    str_table_body += " days ago";
                    str_table_body += "</span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].getamo);
                    else
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    str_table_body += trx_amount.toFixed(basic.decimal);     
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += 'SMC';
                    str_table_body += '</span>';
                    str_table_body += "</span></div></td>";
                    
                    str_table_body += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    else
                        trx_amount = parseFloat(trx_all[i].getamo);
                    str_table_body += trx_amount.toFixed(basic.decimal);
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += trx_all[i].remark;
                    str_table_body += "</span>";
                    str_table_body += "</sapn></div></td>";

                    if(trx_all[i].status == 2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                        str_table_body += "Success";
                    }
                    else if(trx_all[i].status == 1){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                        str_table_body += "Pending";
                    }
                    else if(trx_all[i].status == -2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                        str_table_body += "Declined";
                    }   
                        
                    str_table_body += "</span></div></td>";
                    str_table_body += "</tr>";
                }
            }
        }
        $("#table-id-body").append(str_table_body);
        $("#table-id").DataTable();
    });

    $('#withdraw-decline').on('click', function () {
        $("#table-id").DataTable().destroy();
        $("#table-id-body").empty();
        var str_table_body = "";
        for(var i = 0; i < trx_all.length; i ++){
            if(trx_all[i].user_id == user_id)
            {
                if(trx_all[i].type == 0 && trx_all[i].status == -2)
                {
                    str_table_body += "<tr>";
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
                    str_table_body += trx_all[i].trx;
                    str_table_body += "</a></span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
                    var today = new Date();
                    var created_day = new Date(trx_all[i].created_at);
                    var diff_time = today.getTime() - created_day.getTime();
                    var diff_day = diff_time / (1000 * 3600 * 24);
                    diff_day = parseInt(diff_day);
                    str_table_body += diff_day;
                    str_table_body += " days ago";
                    str_table_body += "</span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].getamo);
                    else
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    str_table_body += trx_amount.toFixed(basic.decimal);     
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += 'SMC';
                    str_table_body += '</span>';
                    str_table_body += "</span></div></td>";
                    
                    str_table_body += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    else
                        trx_amount = parseFloat(trx_all[i].getamo);
                    str_table_body += trx_amount.toFixed(basic.decimal);
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += trx_all[i].remark;
                    str_table_body += "</span>";
                    str_table_body += "</sapn></div></td>";

                    if(trx_all[i].status == 2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                        str_table_body += "Success";
                    }
                    else if(trx_all[i].status == 1){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                        str_table_body += "Pending";
                    }
                    else if(trx_all[i].status == -2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                        str_table_body += "Declined";
                    }   
                        
                    str_table_body += "</span></div></td>";
                    str_table_body += "</tr>";
                }
            }
        }
        $("#table-id-body").append(str_table_body);
        $("#table-id").DataTable();
    });

    $('#deposite-total').on('click', function () {
        $("#table-id").DataTable().destroy();
        $("#table-id-body").empty();
        var str_table_body = "";
        for(var i = 0; i < trx_all.length; i ++){
            if(trx_all[i].user_id == user_id)
            {
                if(trx_all[i].type == 1)
                {
                    str_table_body += "<tr>";
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
                    str_table_body += trx_all[i].trx;
                    str_table_body += "</a></span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
                    var today = new Date();
                    var created_day = new Date(trx_all[i].created_at);
                    var diff_time = today.getTime() - created_day.getTime();
                    var diff_day = diff_time / (1000 * 3600 * 24);
                    diff_day = parseInt(diff_day);
                    str_table_body += diff_day;
                    str_table_body += " days ago";
                    str_table_body += "</span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].getamo);
                    else
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    str_table_body += trx_amount.toFixed(basic.decimal);     
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += 'SMC';
                    str_table_body += '</span>';
                    str_table_body += "</span></div></td>";
                    
                    str_table_body += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    else
                        trx_amount = parseFloat(trx_all[i].getamo);
                    str_table_body += trx_amount.toFixed(basic.decimal);
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += trx_all[i].remark;
                    str_table_body += "</span>";
                    str_table_body += "</sapn></div></td>";

                    if(trx_all[i].status == 2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                        str_table_body += "Success";
                    }
                    else if(trx_all[i].status == 1){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                        str_table_body += "Pending";
                    }
                    else if(trx_all[i].status == -2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                        str_table_body += "Declined";
                    }   
                        
                    str_table_body += "</span></div></td>";
                    str_table_body += "</tr>";
                }
            }
        }
        $("#table-id-body").append(str_table_body);
        $("#table-id").DataTable();
    });

    $('#deposite-pending').on('click', function () {
        $("#table-id").DataTable().destroy();
        $("#table-id-body").empty();
        var str_table_body = "";
        for(var i = 0; i < trx_all.length; i ++){
            if(trx_all[i].user_id == user_id)
            {
                if(trx_all[i].type == 1 && trx_all[i].status == 1)
                {
                    str_table_body += "<tr>";
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
                    str_table_body += trx_all[i].trx;
                    str_table_body += "</a></span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
                    var today = new Date();
                    var created_day = new Date(trx_all[i].created_at);
                    var diff_time = today.getTime() - created_day.getTime();
                    var diff_day = diff_time / (1000 * 3600 * 24);
                    diff_day = parseInt(diff_day);
                    str_table_body += diff_day;
                    str_table_body += " days ago";
                    str_table_body += "</span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].getamo);
                    else
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    str_table_body += trx_amount.toFixed(basic.decimal);     
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += 'SMC';
                    str_table_body += '</span>';
                    str_table_body += "</span></div></td>";
                    
                    str_table_body += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    else
                        trx_amount = parseFloat(trx_all[i].getamo);
                    str_table_body += trx_amount.toFixed(basic.decimal);
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += trx_all[i].remark;
                    str_table_body += "</span>";
                    str_table_body += "</sapn></div></td>";

                    if(trx_all[i].status == 2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                        str_table_body += "Success";
                    }
                    else if(trx_all[i].status == 1){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                        str_table_body += "Pending";
                    }
                    else if(trx_all[i].status == -2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                        str_table_body += "Declined";
                    }   
                        
                    str_table_body += "</span></div></td>";
                    str_table_body += "</tr>";
                }
            }
        }
        $("#table-id-body").append(str_table_body);
        $("#table-id").DataTable();
    });

    $('#deposite-declined').on('click', function () {
        $("#table-id").DataTable().destroy();
        $("#table-id-body").empty();
        var str_table_body = "";
        for(var i = 0; i < trx_all.length; i ++){
            if(trx_all[i].user_id == user_id)
            {
                if(trx_all[i].type == 1 && trx_all[i].status == -2)
                {
                    str_table_body += "<tr>";
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-lead'><a href='#'>#";
                    str_table_body += trx_all[i].trx;
                    str_table_body += "</a></span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col tb-col-md'><span class='tb-sub'>";
                    var today = new Date();
                    var created_day = new Date(trx_all[i].created_at);
                    var diff_time = today.getTime() - created_day.getTime();
                    var diff_day = diff_time / (1000 * 3600 * 24);
                    diff_day = parseInt(diff_day);
                    str_table_body += diff_day;
                    str_table_body += " days ago";
                    str_table_body += "</span></div></td>";
            
                    str_table_body += "<td><div class='nk-tb-col'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].getamo);
                    else
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    str_table_body += trx_amount.toFixed(basic.decimal);     
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += 'SMC';
                    str_table_body += '</span>';
                    str_table_body += "</span></div></td>";
                    
                    str_table_body += "<td><div class='nk-tb-col tb-col-lg'><span class='tb-sub tb-amount'>";
                    if(trx_all[i].type > 0)
                        trx_amount = parseFloat(trx_all[i].amountpaid);
                    else
                        trx_amount = parseFloat(trx_all[i].getamo);
                    str_table_body += trx_amount.toFixed(basic.decimal);
                    str_table_body += "<span class='tb-sub text-primary'>";
                    str_table_body += trx_all[i].remark;
                    str_table_body += "</span>";
                    str_table_body += "</sapn></div></td>";

                    if(trx_all[i].status == 2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-success'>";
                        str_table_body += "Success";
                    }
                    else if(trx_all[i].status == 1){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-warning'>";
                        str_table_body += "Pending";
                    }
                    else if(trx_all[i].status == -2){
                        str_table_body += "<td><div class='nk-tb-col'><span class='badge badge-dot badge-dot-xs badge-danger'>";
                        str_table_body += "Declined";
                    }   
                        
                    str_table_body += "</span></div></td>";
                    str_table_body += "</tr>";
                }
            }
        }
        $("#table-id-body").append(str_table_body);
        $("#table-id").DataTable();
    });
})


