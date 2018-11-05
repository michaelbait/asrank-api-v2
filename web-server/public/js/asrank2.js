let RankUtil = (function($){
    "use strict";

    let ver  = "0.0.1";
    let name = 'RankUtil';

    let dataset_promise = fetch_asrank(RESTFUL_DATABASE_URL+"/ds?verbose")
        .then(response => response.data[0]);

    // Ajax utils
    async function fetch_asrank(url) {
        return fetch(url)
            .then(response => {
                if (!response.ok) { throw response }
                return response.json()
            })
            .then(response => {
                if (response == null || typeof(response) !== "object" || (response instanceof Array) || (response instanceof Date) || !("data" in response)) {
                    throw new Error("malformed results:"+":"+escape_html(JSON.stringify(result)));
                }
                if (response.error.toString() !== "null" && response.error.toString() !== "no matches") {
                    throw new Error(response.error.toString());
                }
                return response;
            }).catch(err => {
                console.log("Error:"+url);
                console.log(err);
                throw err;
            });
    }
    function get_json(url, div_container, func) {
        $.getJSON(url, function (result) {
            if (result == null || typeof(result) !== "object" || (result instanceof Array) || (result instanceof Date) || !("data" in result)) {
                result = {
                    "error":"malformed result:"+":"+escape_html(JSON.stringify(result))
                    ,"data":null
                }
            }
            if (result.error.toString() !== "null" && result.error.toString() !== "no matches") {
                print_error(result.error.toString(), div_container);
            }

            return func(result);

        }).error(function(data) {
            let result = {
                "error":+JSON.stringify(data),
                "data":null
            }
            print_error(result.error);
        });
    }

    // Private Utils
    function host_url() {
        let url = location.protocol+"//"+location.hostname;
        if (location.port.length > 0) {
            url += ":"+location.port;
        }
        return url;
    }
    function escape_html(string) {
        return string
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    function add_param(url, param) {
        var re = RegExp("\\?");
        if (re.test(url)) {
            url += "&"+param;
        } else {
            url += "?"+param;
        }
        return url;
    }
    function update_param ( url, key, value ) {
        let found = false;
        let re = RegExp ( "\\?" );
        let new_url = "";

        if ( re.test ( url ) ) {
            let split1 = url.split ( "?" );
            let split2 = split1[1].split ( "&" );

            let params = [];
            for ( let i = 0; i < split2.length; i++ ) {
                let tmp = split2[i].split ( "=" );
                if ( tmp[0] === key ) {
                    params.push(key+"="+value);
                    found = true;
                } else
                    params.push(split2[i]);
            }
            if ( found === false )
                params.push(key+"="+value);

            new_url = split1[0] + "?" + params.join("&");
        } else
            new_url = url + "?" + key + "=" + value;

        return ( new_url );
    }
    function print_error (message, div_container = null) {
        if (div_container == null) {
            div_container = $('#default_error_location');
            div_container.css({'visibility':'visible'});
        }
        div_container.append('<div class="asrank-result-error"> ERROR:'+message+'</div>');
    }
    function null_unknown(string) {
        if (string == null) {
            return '<span class="asrank-unknown">unknown</span>';
        }
        return string;
    }
    function page_links_add(div_container, html_url, page_number, page_count, objects_total) {
        if (objects_total <= page_count) {
            return;
        }
        let page_numbers = [];
        let page_last = Math.round(objects_total / page_count);
        if (page_last * page_count < objects_total) {
            page_last += 1
        }
        let page_first = 1;

        //console.log(objects_total, page_count, page_last);

        let page_middle = page_number;
        if (page_middle > page_last) {
            page_middle = page_last - PAGE_NUMBER_RANGE;
        } else if (page_middle < page_first) {
            page_middle = page_first + PAGE_NUMBER_RANGE;
            page_numbers.push(page_number);
            if (page_first-1 > page_number) {
                page_numbers.push("..");
            }
        }

        let page_start = page_first;
        if (page_middle-PAGE_NUMBER_RANGE-1 > page_first) {
            page_start=page_middle-PAGE_NUMBER_RANGE;
            page_numbers.push(page_first, "..");
        }
        for (let i = page_start; i <= page_middle; i++) {
            page_numbers.push(i);
        }

        let page_end = page_last;
        if (page_middle+PAGE_NUMBER_RANGE+1 < page_end) {
            page_end = page_middle + PAGE_NUMBER_RANGE;
        }
        for (let i = page_middle+1; i <= page_end;i++) {
            page_numbers.push(i);
        }
        if (page_end !== page_last) {
            page_numbers.push("..",page_last);
        }

        if (page_middle < page_number) {
            if (page_end+1 < page_number) {
                page_numbers.push("..");
            }
            page_numbers.push(page_number);
        }
        //console.log([page_first,page_start,page_number,page_end,page_last].join(","))
        //alert([page_first,page_start,page_number,page_end,page_last].join(","))
        let page_div = $('<div class="asrank-asns-table-pages-links"></div>');
        div_container.append(page_div);

        //let page_url = update_param(html_url,"page=");
        let li;
        let ul = $( '<ul class="pagination pagination-sm"></ul>' );

        $.each(page_numbers, function (i, page) {
            let page_url = add_param(html_url, "verbose");
            page_url = update_param(html_url,"page_number", page);

            if (page === "..") {
                //page_div.append(' .. ');
                li = $( '<li class="page-item page-last-separator disabled"><a class="page-link" href="' + page_url + '">' + page + '</a></li>' );
            } else if (page === page_number) {
                //page_div.append(" "+page+" ");
                li = $( '<li class="page-item active"><a class="page-link" href="' + page_url + '">' + page + '</a></li>' );
            } else {
                //page_div.append(' <a href="'+page_url+page+'">'+page+'</a> ');
                li = $( '<li class="page-item"><a class="page-link" href="' + page_url + '">' + page + '</a></li>' );
            }
            ul.append ( li );
        });

        let nav = $( '<nav></nav>' );
        nav.append ( ul );
        div_container.append ( nav );
    }
    function tree_create_leaves(tree_headers, leaves = []) {
        tree_headers.forEach(function(i, key) {
            let obj = tree_headers[key];
            if (obj.children === undefined){
                leaves.push(obj);
            } else {
                tree_create_leaves(obj.children, leaves);
            }
        });

        return leaves;
    }
    function tree_create_header_html(current_url, table, tree_headers, sort, depth = 0, rows = [] ) {
        let row = null;
        if (depth >= rows.length) {
            rows[depth] = row = $('<tr></tr>');
            table.append(row);
        } else {
            row = rows[depth];
        }
        let sort_types = {
            "rank":"rank",
            "asns":"customer_cone_asns",
            "prefixes":"customer_cone_prefixes",
            "addresses":"customer_cone_addresses",
            "transit":"degree_transit" };

        tree_headers.forEach(function(i, key) {
            let obj = tree_headers[key];
            let sort_html = "";

            // If sortable column header, add sort icon...
            if ( obj.id in sort_types && obj.colspan === 1 && typeof obj.percentage_bar === 'undefined' ) {
                let st = sort_types[obj.id];
                let uri = URI(current_url);
                let url = update_param(current_url, "sort", st);
                let sort_symbol = '&#8711;';

                if ( sort_types[obj.id] === sort ) {
                    if ( sort.startsWith("-")){
                        sort = sort.substr(1, sort.length);
                        url = update_param ( url, "sort", sort);
                        if ( sort === "rank")
                            sort_symbol = '&#9660;';
                        else
                            sort_symbol = '&#9650;';
                    } else {
                        url = update_param ( url, "sort", '-'+sort);
                        if ( sort === "rank" )
                            sort_symbol = '&#9650;';
                        else
                            sort_symbol = '&#9660;';
                    }
                } else if ( sort_types[obj.id] === "rank" ) {
                    sort_symbol = '&#916;';
                }

                sort_html = '&nbsp;<a href="'+url+'"><span style="font-weight:normal;font-size:80%">'+sort_symbol+'&nbsp;</span></a>';
            }
            let th = $('<th>'+obj.label+sort_html+'</th>');

            row.append(th);
            th.attr("colspan",obj.colspan);
            th.attr("rowspan", obj.rowspan);
            if (obj.colspan === 3 && obj.rowspan === 1) th.addClass("d-sm-table-cell");

            if (obj.children !== undefined){
                let dim = tree_create_header_html(current_url, table, obj.children, sort, depth+1, rows );
            }
        });
    }
    function create_table_html(row, leaves, item, mode, dataset) {
        if ("data" in item) item = item.data;
        let html_url = host_url();
        for (let i in leaves ) {
            let td_class = "";
            if(leaves.hasOwnProperty(i)){
                let id = leaves[i]["id"];
                if ("percentage_bar" in leaves[i]) {
                    let percentage_html = "<td>"+null_unknown(null)+"</td>";
                    if (dataset != null && id in dataset) {
                        percentage_html = create_percentage_html(leaves[i], item, mode, dataset)
                    }
                    row.append(percentage_html);
                } else {
                    let val = null;
                    let func = leaves[i]["func"];
                    if (null != func) {
                        val = func(mode,html_url,item);
                    } else {
                        val = item[id];
                    }

                    if ("td_class" in leaves[i]) {
                        td_class = leaves[i]["td_class"];
                    }

                    let colspan = "";
                    if ("colspan" in leaves[i]) {
                        colspan = 'colspan="'+leaves[i].colspan+'"';
                    }
                    row.append('<td class="' + td_class +'" '+colspan+'>'+null_unknown(val)+'</td>');
                }
            }
        }
    }
    function create_percentage_html(leaf, item, mode, dataset, style="") {

        let val = item[leaf['id']];
        if ("func" in leaf) {
            val = leaf["func"](mode,host_url(),item);
        }
        let den = dataset['id'];

        let content = "";
        if (val == null || val === 'undefined' || den == null || den === 'undefined') {
            content = null_unknown(null);
        } else {
            let fraction = (100 * val) / den;
            let result = Math.round(fraction * 100) / 100;

            content = "<span class='perc'>"+result+"%</span><div class='perc' style='width:"+fraction+"%'>&nbsp;</div>";
        }
        //return "<td class='d-none d-md-table-cell perc'" + style +">"+content+"</td>\n";
        return "<td class='perc'" + style +">"+content+"</td>\n";
    }

    function show_loader(){
        let img = '<img id="loader" src="../images/load_radar.gif">';
        $('.ds_table').append(img);
    }
    function hide_loader(){
        $('#loader').remove();
    }

    // Event Handlers
    // $(document).on('click', '.asrank-asns-table tbody tr th', function (e) {
    //     e.preventDefault();
    //     e.stopPropagation();
    //     let url = $(e.target).find('a').attr('href');
    //     let uri = URI(url );
    //     let params = uri.query(true);
    //     if(params['sort'] && params['sort'].startsWith("-")){
    //         params['sort'] = params['sort'].substr(1);
    //     }else{
    //         params['sort'] = '-'+params['sort'];
    //     }
    //     uri.setQuery(params);
    //     window.location.href = uri.href();
    //     return true;
    // });

    // PUBLIC DATA
    return {
        version: function(){
            let ex = name + " ver." + ver;
            console.info(ex);
            return ex;
        },
        null_unknown(str) {
            if (str == null) {
                return '<span class="asrank-unknown">unknown</span>';
            }
            return str;
        },

        dataset_information: function () {
            let div = $('#footer-dataset');
            dataset_promise.then(dataset => {
                // populates date/time, ip protocol inside footer
                let date = dataset.date.replace(/(....)-(..)-(..)(.+)/,"$1/$2/$3");
                div.append(date+"<br>");
                let num = '';
                if (dataset['address_family'] === "AF_INET") {
                    num = '4';
                } else if (dataset['address_family'] === "AF_INET6") {
                    num = '6';
                } else {
                    num = '?';
                }

                div.append("AS Rank IPv" + num);
                document.getElementById('footer-dataset')
                    .setAttribute('title', "The approximate date around which the AS relationship data was inferred from IPv" + num + " BGP AS paths.");
                document.getElementById('footer-dataset')
                    .setAttribute('data-original-title', "The approximate date around which the AS relationship data was inferred from IPv" + num + " BGP AS paths.");

                div.ready(function(){
                    $('[rel="tooltip"]').tooltip();
                });

                // dataset - populate data sources inside dvi
                let sources_div = $('.asrank-data-sources-set');
                if (sources_div) {
                    dataset.sources.forEach( source => {
                        if(source && source['date'] && source['name']){
                            source['date'] = source['date'].length === 8 ?
                                source['date'].replace(/(....)(..)(..)/,"$1/$2/$3") :
                                source['date'].replace(/(....)(..)/,"$1/$2");
                            sources_div.append("<span><a href=\"" + source.url + "\">" + source['name']
                                + "</a> " + source.date + "</span><span class=\"asrank-data-sources-spacer\">|</span>");
                        }
                    });
                    sources_div.children().last().remove();
                }

                let dataset_div = $('#dataset_table');
                if (dataset_div) {
                    let table = $('<table class="asrank-info-table table-condensed" border="0">');
                    dataset_div.append(table);

                    let label_values = [
                        ["date", date],
                        ["number of ASNs",dataset['asns']],
                        ["number of organizations",dataset['orgs']]
                    ];
                    label_values.forEach( function (label_value) {
                        table.append($('<tr><th>'+label_value[0]+'</th><td>'+label_value[1]+'</td></tr>'));
                    });
                }
                return dataset;
            });
        },
        build_info_table: function (mode, id, params) {
            const tree_headers = tree_get_table(mode);
            const leaves = tree_create_leaves(tree_headers);
            let org_info = params['org_info'];
            let page_number = parseInt(params['page_number'], 10);
            let page_size = parseInt(params['page_size'], 10);
            let sort = params['sort'] || 'rank';

            let current_url = host_url() + "/";
            let database_url = RESTFUL_DATABASE_URL +"/";
            if (mode === "org_members") {
                current_url += "orgs/"+id;
                database_url += "orgs/"+id+"/members";
            } else if (mode === "asns") {
                current_url += "asns";
                database_url += "asns";
                database_url = add_param(database_url, 'ranked');
            }else if (mode === "orgs") {
                current_url += "orgs";
                database_url += "orgs";
                database_url = add_param(database_url, 'ranked');
            } else if (mode === "asn_neighbors") {
                current_url += "asns/" + id;
                database_url += "asns/"+id+"/links";
            } else if (mode === "asn_search") {
                current_url += "asns?name="+id+"&type=search";
                database_url += "asns?name="+id;
            } else {
                if (mode === "asns_top_ten") {
                    mode = "asns";
                    database_url += "asns";
                } else if (mode !== "asns") {
                    current_url += "asns";
                    console.log("ERROR: unknown mode:"+mode);
                }
                database_url = add_param(database_url, 'ranked');
            }
            database_url = add_param(database_url, 'verbose');

            if (page_number != null) {
                database_url = add_param(database_url, "page_number="+page_number);
                current_url = add_param(current_url, "page_number="+page_number);
            }
            if (page_size != null) {
                database_url = add_param(database_url, "page_size="+page_size);
                current_url = add_param(current_url, "page_size="+page_size);
            }


            if (sort != null && sort.length > 0) {
                database_url = add_param(database_url, "sort="+sort);
                current_url = add_param(current_url, "sort="+sort);
            }

            let div = $("#"+mode+"_table");
            let table = $('<table class="asrank-asns-table table table-responsive table-bordered table-hover d-table table-striped"></table>');

            show_loader();

            get_json(RESTFUL_DATABASE_URL+"/ds?verbose", div, function (result) {
                let family = "IPv4";
                let dataset = null;

                if (result.data != null) {
                    dataset = result.data[0];
                    if (dataset['family'] === "AF_INET6") dataset[family] = "IPv6";
                }

                if (page_number < 1) page_number = 1;
                let page_links_header = $('<div></div>');
                div.append(page_links_header);

                if (!sort) sort = "rank";

                get_json(database_url, div, function(items) {

                    let total = items['metadata']['total'];
                    // if ("total" in items)
                    //     total = items.total;

                    page_links_add(page_links_header, current_url, page_number, page_size, total);
                    tree_create_header_html ( current_url, table, tree_headers, sort);

                    hide_loader();
                    div.append(table);

                    if (mode === "org_members") {
                        const org_tree = tree_get_table("org");
                        const org_leaves = tree_create_leaves(org_tree);
                        org_leaves[0].colspan = 3;

                        let row = $('<tr></tr>');
                        table.append(row);
                        create_table_html(row, org_leaves, org_info, "orgs", dataset);
                    }

                    if (total === 0) {
                        table.append('<tr><td class="asrank-row-none" colspan="'+leaves.length+'">none found</td></tr>');
                    } else {
                        let pp = items['data'].length;
                        for (let i = 0; i < pp; i++) {
                            let row = $('<tr></tr>');
                            table.append(row);
                            create_table_html(row, leaves, items['data'][i], mode, dataset);
                        }
                    }
                    page_links_add(div, current_url, page_number, page_size, total);

                });
            });

        }
    };

})(window.jQuery);