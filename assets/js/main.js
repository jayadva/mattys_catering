jQuery( document ).ready(function() {
    // Set up delegated event handlers once — works for all current and future cart items
    addquantity();
    subtractquantity();
    removedItem();

    if (jQuery("#sidebarcartitemlist").length) {
        loadCartItems();
    }
    
    jQuery('.btn-addcart').click(function() {
        var productId = jQuery(this).data('productmenuid');
        var catid = jQuery(this).data('catid');
        var catname = jQuery(this).data('catname');

        jQuery('#mattysmodalbody').html("");
        

        jQuery.ajax({
			type: "POST",
            url: mattys_ajax.ajax_url,
            data: {
                action: 'productmenumodal',
                productId: productId,
                catid: catid,
                catname: catname,
                nonce: mattys_ajax.nonce
            },beforeSend: function() { 
                
            },
            success: function (response) {
                const contents = response.data;
                if(response.success == 1) {
                    jQuery('#mattysmodalbody').html(contents);

                    addItemToCart();
                    validateModalForm();
                    jQuery('#productcartform #portion_size, #productcartform #cut_preference').on('change', validateModalForm);
                    
                } else {
                    jQuery('#mattysmodalbody').html(contents);
                }

                jQuery('.btn-modifysandwich').on('click', (function() {
                    if (jQuery(".btn-modifysandwich").hasClass("active")) {
                        jQuery(".btn-modifysandwich").removeClass("active");
                        jQuery("#productoptionaddons").removeClass("active");
                    } else {
                        jQuery(".btn-modifysandwich").addClass("active"); 
                        jQuery("#productoptionaddons").addClass("active");
                    }
                }));
            }
        });
    });

    if (jQuery("#mattysmainbody").length) {
        new SimpleBar(jQuery('#mattysmainbody')[0], { autoHide: true });
    }

    jQuery("#mattyscartforminfo").validate({
        rules: {
            fullname: "required",
            phonenumber: {
                required: true,
                minlength: 2,
            },
            emailaddress: {
                required: true,
                email: true
            },
            totalpeoplecatering: "required",
            deliverydate: "required",
            deliverytime: "required",
            deliveryaddress: {
                required:'#delivery:checked'
            }
        },
        messages: {
            email: "Please enter a valid email address",
            deliverydate: "Date is required",
            deliverytime: "Time is required",
        },
        submitHandler: function(form) {
            let cartitems = localStorage.getItem('cartitems'); 
            let beverageaddons = jQuery('input[name="beverageadddons[]"]:checked').map(function() {
                return jQuery(this).val();
            }).get();
            let deliveryAddress = "";
            let pickupdelivery = jQuery('input[name="pickupdelivery"]:checked').val();

            if (pickupdelivery == 'Delivery') {
                deliveryAddress = jQuery("#deliveryaddress").val();
            }

            const getTotalAmount= jQuery("#carttotalwrap .carttotalprice").text();
            const totalAmount = getTotalAmount.toString().replace(/[$\s]/g, '');
            const total = parseFloat(totalAmount) || 0;

            if(total >= 200 ) {
                if (cartitems) {
                    // Get delivery time and add 3 hours
                    let deliveryTimeInput = jQuery("#deliverytime").val();
                    let adjustedDeliveryTime = deliveryTimeInput;
                    
                    if (deliveryTimeInput) {
                        // Parse the time and add 3 hours
                        let timeParts = deliveryTimeInput.match(/(\d+):(\d+)\s*(AM|PM)?/i);
                        if (timeParts) {
                            let hours = parseInt(timeParts[1]);
                            let minutes = timeParts[2];
                            let period = timeParts[3] ? timeParts[3].toUpperCase() : '';
                            
                            // Convert to 24-hour format if AM/PM is present
                            if (period === 'PM' && hours !== 12) {
                                hours += 12;
                            } else if (period === 'AM' && hours === 12) {
                                hours = 0;
                            }
                            
                            // Add 3 hours
                            hours += 3;
                            
                            // Handle day overflow
                            if (hours >= 24) {
                                hours -= 24;
                            }
                            
                            // Convert back to 12-hour format with AM/PM
                            let newPeriod = hours >= 12 ? 'PM' : 'AM';
                            let displayHours = hours % 12;
                            if (displayHours === 0) displayHours = 12;
                            
                            adjustedDeliveryTime = displayHours + ':' + minutes + ' ' + newPeriod;
                        }
                    }
                    
                    jQuery.ajax({
                        type: "POST",
                        url: mattys_ajax.ajax_url,
                        data: {
                            action: 'mattysorderemail',
                            fullname: jQuery("#fullname").val(),
                            phonenumber: jQuery("#phonenumber").val(),
                            emailaddress: jQuery("#emailaddress").val(), 
                            companyorganization: jQuery("#companyorganization").val(),
                            totalpeoplecatering: jQuery("#totalpeoplecatering").val(),
                            deliverydate: jQuery("#deliverydate").val(), 
                            deliverytime: adjustedDeliveryTime, 
                            pickupdelivery: pickupdelivery,
                            deliveryAddress: deliveryAddress,
                            dietaryrequirements: jQuery("#dietaryrequirements").val(),
                            specialrequest: jQuery("#specialrequest").val(),
                            totalamount: getTotalAmount,
                            cartitems: cartitems,
                            nonce: mattys_ajax.nonce
                        },
                        beforeSend: function () {
                            jQuery("#errormessage").html('<div class="alert alert-primary" role="alert">Submitting your order, please wait...</div>');
                            jQuery("#btnsubmitquery").prop('disabled', true).text('Submitting...');
                        },
                        success: function (data) {
                            if (data.success == true) {
                                jQuery("#errormessage").html('<div class="alert alert-success" role="alert">' + data.message + '</div>');
                                
                                // Clear the cart after successful submission
                                localStorage.removeItem('cartitems');
                                jQuery('#sidebarcartitemlist').html('');
                                
                                // Redirect if provided
                                if (data.redirect) {
                                    setTimeout(function() {
                                        window.location = data.redirect;
                                    }, 2000);
                                }
                            } else {
                                jQuery("#errormessage").html('<div class="alert alert-danger" role="alert">' + data.errormessage + '</div>');
                                jQuery("#btnsubmitquery").prop('disabled', false).text('SUBMIT ENQUIRY');
                            }
                        },
                        error: function() {
                            jQuery("#errormessage").html('<div class="alert alert-danger" role="alert">An error occurred. Please try again.</div>');
                            jQuery("#btnsubmitquery").prop('disabled', false).text('SUBMIT ENQUIRY');
                        }
                    });
                } else {
                    jQuery("#errormessage").html('<div class="alert alert-danger" role="alert">Please add a product before you can submit the form.</div>');
                }
            } else {
                jQuery("#errormessage").html('<div class="alert alert-danger" role="alert">Minimum order amount not reached!</div>');
                return false;
            }
        }
    });

    // Populate delivery time picker with 30-min intervals, disabling unavailable times
    if (jQuery('#deliverytime').length) {
        var lastPickupMins = 22 * 60; // 10:00 PM
        var now = new Date();
        var todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
        jQuery('#deliverydate').attr('min', todayStr);
        var currentHour = now.getHours();
        var minTotalMins;

        if (currentHour < 9) {
            // Before 9 AM: earliest is 12:00 PM same day
            minTotalMins = 12 * 60;
        } else {
            // 9 AM or later: current time + 3 hour lead time
            var minDate = new Date(now.getTime() + 3 * 60 * 60 * 1000);
            minTotalMins = minDate.getHours() * 60 + minDate.getMinutes();
        }

        // If lead time pushes past last pickup (10 PM), roll over to next day at 12 PM
        if (minTotalMins > lastPickupMins) {
            minTotalMins = 12 * 60;
            var tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            var yyyy = tomorrow.getFullYear();
            var mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
            var dd = String(tomorrow.getDate()).padStart(2, '0');
            var tomorrowStr = yyyy + '-' + mm + '-' + dd;
            jQuery('#deliverydate').val(tomorrowStr).attr('min', tomorrowStr);
        }

        var $select = jQuery('#deliverytime');
        // Generate options from 8:00 AM to 10:00 PM in 30-min intervals
        for (var totalMins = 12 * 60; totalMins <= lastPickupMins; totalMins += 30) {
            var h = Math.floor(totalMins / 60);
            var m = totalMins % 60;
            var period = h >= 12 ? 'PM' : 'AM';
            var displayH = h > 12 ? h - 12 : (h === 0 ? 12 : h);
            var displayM = String(m).padStart(2, '0');
            var label = displayH + ':' + displayM + ' ' + period;
            var $option = jQuery('<option>', { value: label, text: label });
            if (totalMins < minTotalMins) {
                $option.prop('disabled', true);
            }
            $select.append($option);
        }
    }

    jQuery(window).resize(function() {
        console.log('resize window');
    });

    jQuery('#sidebartrigger').on('click', (function() {
        jQuery('#sidebar').addClass('active');
    }));
    
    jQuery('.btn-closesidebar').on('click', (function() {
        jQuery('#sidebar').removeClass('active');
    }));
    
    jQuery('.btn-mattysclose').click(function() {
        jQuery('#mattysprodmenumodal').modal('hide');
    });

    var deliveryaddress = jQuery('input[name="pickupdelivery"]:checked').val();
    if (deliveryaddress == 'Delivery') {
        jQuery("#deliveryaddresswrap").prop("disabled", false);
    }else {
        jQuery("#deliveryaddresswrap").prop("disabled", true);
    }
    jQuery('.pickupdelivery').click(function() {
        var selectedValue = jQuery('input[name="pickupdelivery"]:checked').val();
        
        if (selectedValue == 'Delivery') {
            jQuery("#deliveryaddresswrap").prop("disabled", false);
        }else {
            jQuery("#deliveryaddresswrap").prop("disabled", true);
        }
    });
    jQuery('#btn-confirmorder').on('click', (function(e) {
        e.preventDefault();

        const getTotalAmount= jQuery("#carttotalwrap .carttotalprice").text();
        const totalAmount = getTotalAmount.toString().replace(/[$\s]/g, '');
        const total = parseFloat(totalAmount) || 0;

        if(total >= 200 ) {
            // Redirect to the href value of the button
            const redirectUrl = jQuery("#btn-confirmorder").attr("href");
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        } else {
            jQuery("#btn-confirmorder").text("minimum order amount not reached");

        }

    }));

    
    function addItemToCart() {
        jQuery('.btn-additem').click(function() {
            let displayitem = '';
            let producttitle = jQuery('#productcartform #producttitle').data('producttitle');
            let productimg = jQuery('#productimgsrc').data('productimg');
            let productId = jQuery('#productcartform #productid').val();
            let addondrinks = jQuery('#productcartform #addondrinks').val();
            let catid = jQuery('#productcartform #catid').val();
            let catname = jQuery('#productcartform #catname').val();
            let portion_size = jQuery('#productcartform #portion_size').val();
            let portion_size_price = jQuery('#productcartform #portion_size option:selected').data('price');
            let cut_preference = jQuery('#productcartform #cut_preference').val();

            // Validate required fields
            var hasError = false;
            var $portionSizeField = jQuery('#productcartform #portion_size');
            if ($portionSizeField.is('select') && !portion_size) {
                $portionSizeField.closest('.productoptions').find('.selectwrapper').addClass('field-error');
                hasError = true;
            }
            var $cutPrefField = jQuery('#productcartform #cut_preference');
            if ($cutPrefField.length && !cut_preference) {
                $cutPrefField.closest('.productoptions').find('.selectwrapper').addClass('field-error');
                hasError = true;
            }
            if (hasError) { return false; }
            
            let quantity = parseInt(jQuery('#productcartform #prodquantity').val(), 10) || 1;
            let modify_no_sandwich = jQuery('input[name="modify_no_sandwich[]"]:checked').map(function() {
                return jQuery(this).val();
            }).get();
            let modify_addon_sandwich = jQuery('input[name="modify_addon_sandwich[]"]:checked').map(function() {
                return jQuery(this).val();
            }).get();

            let modify_addon_sandwich_amount = jQuery('input[name="modify_addon_sandwich[]"]:checked').map(function() {
                return jQuery(this).data('addonamount');
            }).get();

            if (!portion_size_price) {
                portion_size_price = jQuery('#productcartform #portion_size').data('price');
            }
            console.log(portion_size_price);

            let productmenuid = generateRandomId();
            let cartitems = localStorage.getItem('cartitems');
            let datasetitem = [];
            let btnquantityremove = '';
            let btnquantitydecrement = '';
            
            if (cartitems) {
                let parsed = JSON.parse(cartitems);
                // Ensure it's an array, if not reset to empty array
                datasetitem = Array.isArray(parsed) ? parsed : [];
            }

            // Create new item object
            let newItem = {
                productmenuid: productmenuid,
                productid: productId,
                producttitle: producttitle,
                productimg: productimg,
                portion_size: portion_size,
                portion_size_price: portion_size_price,
                cut_preference: cut_preference,
                quantity: quantity,
                modify_no_sandwich: modify_no_sandwich,
                modify_addon_sandwich: modify_addon_sandwich,
                modify_addon_sandwich_amount: modify_addon_sandwich_amount,
                addondrinks: addondrinks,
                catid: catid,
                catname: catname
            };
            
            // Push new item to the array
            datasetitem.push(newItem);
            
            localStorage.setItem('cartitems', JSON.stringify(datasetitem));
            
            displayitem += '<div class="sidebarcartitem">';

            if(productimg) {
                displayitem += '    <div class="cartlistitemimg">';
                displayitem += '        <img src="'+productimg+'" alt="">';
                displayitem += '    </div>';
            }
            displayitem += '    <div class="cartlistitemdetail">';
            displayitem += '        <div class="cartlisinfo">';

            if (addondrinks == 1) {
                displayitem += '            <h5 class="text-green mb-1">'+producttitle+' ('+catname+')</h5>';
            } else {
                displayitem += '            <h5 class="text-green mb-1">'+producttitle+'</h5>';
            }

            displayitem += '            <div class="cartlistitemdetailtext">';
           
            if (portion_size.length > 0 && portion_size != "") {
                displayitem += '<span> '+portion_size+', </span>';
            }
            if (cut_preference && cut_preference.length > 0) {
                displayitem += '<span>'+cut_preference+'</span>';
            }
            if (modify_no_sandwich.length > 0) {
                displayitem += '<span>, '+modify_no_sandwich.join(', ')+'</span>';
            }
            if (modify_addon_sandwich.length > 0) {
                displayitem += '<span>, '+modify_addon_sandwich.join(', ')+' </span>';
            }
            displayitem += '            </div>';

            if(portion_size_price) {
                displayitem += '        <div class="sidecartportionsizeprice"> '+portion_size_price+' </div>';
            }
            displayitem += '        </div>'; // EOF cartlistdetailwrap
            displayitem += '        <div class="sidequantitywrap">';
            displayitem += '            <div class="input-group quantity-container" style="width: 100px;">';
            
            if(quantity == 1) {
                btnquantitydecrement = 'btnhide';
            } else {
                btnquantityremove = 'btnhide';
            }
            
            displayitem += '                <button class="btn btnquantity removeitem-btn '+btnquantityremove+'" type="button" data-productmenuid="'+productmenuid+'"></button>';
            displayitem += '                <button class="btn btnquantity decrement-btn '+btnquantitydecrement+'" type="button">&minus;</button>';
            displayitem += '                <input type="text" class="form-control text-center qty-input prodquantity" name="prodquantity" id="prodquantity" value="'+quantity+'" min="1" max="100" data-productmenuid="'+productmenuid+'">';
            displayitem += '                <button class="btn btnquantity increment-btn" type="button">&plus;</button>';
            displayitem += '            </div>';
            displayitem += '        </div>'; // EOF sidequantitywrap

            displayitem += '    </div>';
            
            displayitem += '</div>';
            
            if (addondrinks == 1) {
                jQuery('#addondrinkstitle').html('ADD ON DRINKS');
                jQuery('#sidebarcartitemlistdrinks').prepend(displayitem);
            } else {
                jQuery('#sidebarcartitemlist').prepend(displayitem);
            }
            
            
            if(jQuery('#sidebarcartitemlist').length > 0) {
                jQuery('#btn-confirmorder').removeClass('disabled-link'); 
            }
            
            // Update cart total
            updateCartTotal();

            jQuery(".btn-close").trigger("click");
                                    
        });
    }
    function validateModalForm() {
        var isValid = true;
        var $portionSize = jQuery('#productcartform #portion_size');
        if ($portionSize.is('select')) {
            if (!$portionSize.val()) {
                isValid = false;
            } else {
                $portionSize.closest('.productoptions').find('.selectwrapper').removeClass('field-error');
            }
        }
        var $cutPref = jQuery('#productcartform #cut_preference');
        if ($cutPref.length) {
            if (!$cutPref.val()) {
                isValid = false;
            } else {
                $cutPref.closest('.productoptions').find('.selectwrapper').removeClass('field-error');
            }
        }
        jQuery('.btn-additem').toggleClass('btn-additem-locked', !isValid);
    }
    function removedItem() {
        jQuery(document).off('click.removeitem', '.removeitem-btn').on('click.removeitem', '.removeitem-btn', function() {
            let productmenuid = jQuery(this).data('productmenuid');
            let cartitems = localStorage.getItem('cartitems');

            if (cartitems) {
                let parsed = JSON.parse(cartitems);
                if (Array.isArray(parsed)) {
                    parsed = parsed.filter(item => item.productmenuid !== productmenuid);
                    localStorage.setItem('cartitems', JSON.stringify(parsed));
                }
            }

            jQuery(this).closest('.sidebarcartitem').remove();

            let newcartitems = localStorage.getItem('cartitems');

            if (newcartitems == "" || newcartitems == "[]") {
                jQuery('#btn-confirmorder').addClass('disabled-link');
                jQuery('#btnsubmitquery').addClass('disabled-link');
            }

            // Update cart total after removing item
            updateCartTotal();
        });
    }
    function addquantity() {
        jQuery(document).off('click.increment', '.increment-btn').on('click.increment', '.increment-btn', function(e) {
            e.preventDefault();
            var $input = jQuery(this).closest('.quantity-container').find('.prodquantity');
            var productmenuid = $input.data('productmenuid');
            var currentVal = parseInt($input.val(), 10);

            if (isNaN(currentVal)) {
                currentVal = 1;
            }

            // Increment the value
            let quantity = currentVal + 1;
            $input.val(quantity);

            if (currentVal == 1) {
                jQuery(this).closest('.sidebarcartitem').find('.removeitem-btn').addClass('btnhide');
                jQuery(this).closest('.sidebarcartitem').find('.decrement-btn').removeClass('btnhide');
            }

            if (productmenuid) {
                updateItemInCart(productmenuid, quantity);
                updateCartTotal();
            }
        });
    }
    function subtractquantity() {
        jQuery(document).off('click.decrement', '.decrement-btn').on('click.decrement', '.decrement-btn', function(e) {
            e.preventDefault();
            var $input = jQuery(this).closest('.quantity-container').find('.prodquantity');
            var currentVal = parseInt($input.val(), 10);
            var minVal = parseInt($input.attr('min'), 10) || 1;
            var productmenuid = $input.data('productmenuid');
            var newquatityvalue = "";

            // Ensure the value does not go below the minimum (e.g., 1 or 0)
            if (!isNaN(currentVal) && currentVal > minVal) {
                newquatityvalue = currentVal - 1;

                $input.val(newquatityvalue);

                if (newquatityvalue == 1) {
                    jQuery(this).closest('.sidebarcartitem').find('.removeitem-btn').removeClass('btnhide');
                    jQuery(this).closest('.sidebarcartitem').find('.decrement-btn').addClass('btnhide');
                } else {
                    jQuery(this).closest('.sidebarcartitem').find('.removeitem-btn').addClass('btnhide');
                    jQuery(this).closest('.sidebarcartitem').find('.decrement-btn').removeClass('btnhide');
                }
            } else if (isNaN(currentVal)) {
                $input.val(minVal);
                newquatityvalue = minVal;
            }

            if (productmenuid && newquatityvalue !== "") {
                updateItemInCart(productmenuid, newquatityvalue);
                updateCartTotal();
            }
        });
    }
    function generateRandomId(prefix) {
        prefix = prefix || 'mattysid-';
        // Combine current timestamp with a random number for better uniqueness
        return prefix + Date.now().toString(36) + Math.random().toString(36).substr(2, 9);
    }
    function removeItemFromCart(productmenuid) {
        let cartitems = localStorage.getItem('cartitems');
        if (cartitems) {
            let parsed = JSON.parse(cartitems);
            if (Array.isArray(parsed)) {
                parsed = parsed.filter(item => item.productmenuid !== productmenuid);
                localStorage.setItem('cartitems', JSON.stringify(parsed));
            }
        }
    }
    function updateItemInCart(productmenuid, quantity) {
        let cartitems = localStorage.getItem('cartitems');
        if (cartitems && productmenuid && quantity != "") {
            let parsed = JSON.parse(cartitems);
            if (Array.isArray(parsed)) {
                // Find the item by productmenuid and update its quantity
                for (let i = 0; i < parsed.length; i++) {
                    if (parsed[i].productmenuid === productmenuid) {
                        parsed[i].quantity = quantity;
                        break;
                    }
                }
                // Save updated cart back to localStorage
                localStorage.setItem('cartitems', JSON.stringify(parsed));
            }
        }
    }
    function loadCartItems() {
        let cartitems = localStorage.getItem('cartitems');
        let btnquantityremove = '';
        let btnquantitydecrement = '';

        if (cartitems && cartitems != "[]") {
            let parsed = JSON.parse(cartitems);
            let displayitem = '';
            let displaydrinks = '';
            
            if (Array.isArray(parsed)) {
                // Reverse to show newest items first
                parsed.reverse();
                
                let drinks = 0;
                for (let i = 0; i < parsed.length; i++) {
                    btnquantityremove = '';
                    btnquantitydecrement = '';

                    if (parsed[i].addondrinks == 1) {
                        displaydrinks += '<div class="sidebarcartitem">';

                        if(parsed[i].productimg) {
                            displaydrinks += '    <div class="cartlistitemimg">';
                            displaydrinks += '        <img src="'+parsed[i].productimg+'" alt="">';
                            displaydrinks += '    </div>';
                        }

                        displaydrinks += '    <div class="cartlistitemdetail">';
                        displaydrinks += '        <div class="cartlisinfo">';
                        displaydrinks += '              <h5 class="text-green mb-1">'+parsed[i].producttitle+' ('+parsed[i].catname+')</h5>';
                        displaydrinks += '              <div class="cartlistitemdetailtext">';

                        if(parsed[i].portion_size) {
                            displaydrinks += '<span>'+parsed[i].portion_size+'</span>';
                        }

                        if(parsed[i].cut_preference) {
                            displaydrinks += '<span>, '+parsed[i].cut_preference+'</span>';
                        }    

                        if(parsed[i].modify_no_sandwich.length > 0) {
                            displaydrinks += '<span>, '+parsed[i].modify_no_sandwich.join(', ')+' </span>';
                        }

                        if(parsed[i].modify_addon_sandwich.length > 0) {
                            displaydrinks += '<span> '+parsed[i].modify_addon_sandwich.join(', ')+' </span>';
                        }
                        
                        displaydrinks += '              </div>';

                        if(parsed[i].portion_size_price) {
                            displaydrinks += '          <div class="sidecartportionsizeprice"> '+parsed[i].portion_size_price+' </div>';
                        }

                        displaydrinks += '        </div>';
                        displaydrinks += '        <div class="sidequantitywrap">';
                        displaydrinks += '            <div class="input-group quantity-container" style="width: 100px;">';
                        
                        if(parsed[i].quantity == 1) {
                            btnquantitydecrement = 'btnhide';
                        } else {
                            btnquantityremove = 'btnhide';
                        }
                
                        displaydrinks += '                <button class="btn btnquantity removeitem-btn '+btnquantityremove+'" type="button" data-productmenuid="'+parsed[i].productmenuid+'"></button>';
                        displaydrinks += '                <button class="btn btnquantity decrement-btn '+btnquantitydecrement+'" type="button">&minus;</button>';
                        displaydrinks += '                <input type="text" class="form-control text-center qty-input prodquantity" name="prodquantity" id="prodquantity" value="'+parsed[i].quantity+'" min="1" max="100" data-productmenuid="'+parsed[i].productmenuid+'">';
                        displaydrinks += '                <button class="btn btnquantity increment-btn" type="button">&plus;</button>';
                        displaydrinks += '            </div>';
                        displaydrinks += '        </div>';
                        displaydrinks += '    </div>';
                        displaydrinks += '</div>';

                        drinks++;
                    } else {
                        displayitem += '<div class="sidebarcartitem">';

                        if(parsed[i].productimg) {
                            displayitem += '    <div class="cartlistitemimg">';
                            displayitem += '        <img src="'+parsed[i].productimg+'" alt="">';
                            displayitem += '    </div>';
                        }

                        displayitem += '    <div class="cartlistitemdetail">';
                        displayitem += '        <div class="cartlisinfo">';
                        displayitem += '            <h5 class="text-green mb-1">'+parsed[i].producttitle+'</h5>';
                        displayitem += '            <div class="cartlistitemdetailtext">';
                       
                        if(parsed[i].portion_size) {
                            displayitem += '<span class="">'+parsed[i].portion_size+'</span>, ';
                        }
                        if(parsed[i].cut_preference) {
                            displayitem += '<span class="">'+parsed[i].cut_preference+'</span>';
                        }

                        if(parsed[i].modify_no_sandwich.length > 0) {
                            displayitem += '<span>, '+parsed[i].modify_no_sandwich.join(', ')+'</span>';
                        }

                        if(parsed[i].modify_addon_sandwich.length > 0) {
                            displayitem += '<span>, '+parsed[i].modify_addon_sandwich.join(', ')+' </span>';
                        }

                        displayitem += '            </div>';

                        if(parsed[i].portion_size_price) {
                            displayitem += '          <div class="sidecartportionsizeprice"> '+parsed[i].portion_size_price+' </div>';
                        }

                        displayitem += '        </div>';
                        displayitem += '        <div class="sidequantitywrap">';
                        displayitem += '            <div class="input-group quantity-container" style="width: 100px;">';
                        
                        if(parsed[i].quantity == 1) {
                            btnquantitydecrement = 'btnhide';
                        } else {
                            btnquantityremove = 'btnhide';
                        }
                
                        displayitem += '                <button class="btn btnquantity removeitem-btn '+btnquantityremove+'" type="button" data-productmenuid="'+parsed[i].productmenuid+'"></button>';
                        displayitem += '                <button class="btn btnquantity decrement-btn '+btnquantitydecrement+'" type="button">&minus;</button>';
                        displayitem += '                <input type="text" class="form-control text-center qty-input prodquantity" name="prodquantity" id="prodquantity" value="'+parsed[i].quantity+'" min="1" max="100" data-productmenuid="'+parsed[i].productmenuid+'">';
                        displayitem += '                <button class="btn btnquantity increment-btn" type="button">&plus;</button>';
                        displayitem += '            </div>';
                        displayitem += '        </div>';
                        displayitem += '    </div>';
                        displayitem += '</div>';
                    }
                    
                }

                jQuery('#sidebarcartitemlist').append(displayitem);

                if(drinks > 0) {
                    jQuery('#addondrinkstitle').html('ADD ON DRINKS');
                    jQuery('#addondrinkstitle').css('margin-bottom', '32px');
                    jQuery('#sidebarcartitemlistdrinks').append(displaydrinks);
                }

                if(jQuery('#sidebarcartitemlist').length > 0 || jQuery('#sidebarcartitemlistdrinks').length > 0) {
                    jQuery('#btn-confirmorder').removeClass('disabled-link'); 
                    jQuery('#btnsubmitquery').removeClass('disabled-link');
                }

                // Update cart total after loading items
                updateCartTotal();
                
                return parsed;
            }
        } else {
            jQuery('#btn-confirmorder').addClass('disabled-link');
            jQuery('#btnsubmitquery').addClass('disabled-link');
        }
        return [];
    }
    function updateCartTotal() {
        let cartitems = localStorage.getItem('cartitems');
        let total = 0;
        
        if (cartitems && cartitems != "[]") {
            let parsed = JSON.parse(cartitems);
            
            if (Array.isArray(parsed)) {
                parsed.forEach(function(item) {
                    // Parse the price - remove $ and any other non-numeric characters except decimal point
                    let price = 0;
                    if (item.portion_size_price) {
                        // Remove $, commas, and any whitespace, then parse as float
                        let priceString = item.portion_size_price.toString().replace(/[$,\s]/g, '');
                        price = parseFloat(priceString) || 0;
                    }
                    
                    // Add modify_addon_sandwich amounts
                    let addonTotal = 0;
                    if (item.modify_addon_sandwich_amount && Array.isArray(item.modify_addon_sandwich_amount)) {
                        item.modify_addon_sandwich_amount.forEach(function(addonAmount) {
                            if (addonAmount) {
                                // Parse addon amount (remove +, $, whitespace)
                                let addonString = addonAmount.toString().replace(/[$,\s+]/g, '');
                                addonTotal += parseFloat(addonString) || 0;
                            }
                        });
                    }
                    
                    let quantity = parseInt(item.quantity, 10);
                    if (isNaN(quantity) || quantity < 1) { quantity = 1; }
                    // Calculate: (base price + addon amounts) * quantity
                    total += (price + addonTotal) * quantity;
                });
            }
        }
        
        if (total >= 200) {
            jQuery("#btn-confirmorder").text("Continue Enquiry");
        }
        // Format total as currency
        let formattedTotal = '$' + total.toFixed(2);
        
        // Update the total display
        jQuery('.carttotalprice').text(formattedTotal);
        
        return total;
    }
});