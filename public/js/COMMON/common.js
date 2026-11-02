/* Recover tag html */
function recoverTagHtml(escapedHTML) {
    return escapedHTML.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
}

function formatDateTimeVN(value, withTime) {
    if (value == null || value === '') return '';
    var raw = String(value).trim();
    if (/^\d{2}\/\d{2}\/\d{4}/.test(raw)) return raw;

    var hasZone = /(?:Z|[+\-]\d{2}:?\d{2})$/i.test(raw);
    var normalized = raw.indexOf('T') === -1 ? raw.replace(' ', 'T') : raw;
    if (!hasZone && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(normalized)) {
        normalized += '+07:00';
    }

    var date = new Date(normalized);
    if (isNaN(date.getTime())) return raw;

    var options = {
        timeZone: 'Asia/Ho_Chi_Minh',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    };
    if (withTime !== false) {
        options.hour = '2-digit';
        options.minute = '2-digit';
        options.hour12 = false;
    }

    return new Intl.DateTimeFormat('vi-VN', options).format(date);
}

function formatPhoneNumber(phone) {
    // Xoá tất cả ký tự không phải số
    phone = phone.replace(/\D/g, '');

    // Nếu chuỗi có ít hơn 7 ký tự thì không định dạng
    if (phone.length <= 7) return phone;

    // Cắt thành 3 phần: 4 ký tự đầu, 3 ký tự tiếp, phần còn lại
    const part1 = phone.slice(0, 4);
    const part2 = phone.slice(4, 7);
    const part3 = phone.slice(7);

    return `${part1} ${part2} ${part3}`;
}

function isEmpty(value) {
    // Kiểm tra null hoặc undefined
    if (value === null || value === undefined) {
        return true;
    }

    // Kiểm tra chuỗi rỗng
    if (typeof value === 'string' && value.trim() === '') {
        return true;
    }

    // Kiểm tra mảng rỗng
    if (Array.isArray(value) && value.length === 0) {
        return true;
    }

    // Kiểm tra đối tượng rỗng
    if (typeof value === 'object' && Object.keys(value).length === 0) {
        return true;
    }

    // Nếu không thuộc trường hợp nào trong các điều kiện trên
    return false;
}

function toSlugId(str) {
    return str
        .toLowerCase() // chuyển về chữ thường
        .normalize('NFD') // tách dấu
        .replace(/[\u0300-\u036f]/g, '') // xóa dấu
        .replace(/đ/g, 'd') // thay đ -> d
        .replace(/[^a-z0-9]+/g, '-') // thay ký tự không hợp lệ bằng dấu gạch ngang
        .replace(/^-+|-+$/g, ''); // loại bỏ dấu gạch đầu/cuối
}

function sanitizePhoneNumberFromString(input) {
    if (isEmpty(input) == true) return null;
    return input.replace(/\D/g, '');
}

function isNumber(value) {
    return !isNaN(value) && typeof Number(value) === 'number';
}

/**
 * Thay thế phần mở rộng cuối cùng của tên tệp.
 * @param {string} fileName - Tên tệp ban đầu.
 * @returns {string} - Tên tệp với phần mở rộng cuối cùng đã bị xóa.
 */
function replaceLastExtension(fileName, toText) {
    return fileName.replace(/(\.[a-zA-Z0-9]+)$/, toText);
}

function getVietnamDateString_YYYY_MM_DD() {
    const date = new Date();

    // Cộng thêm 7 tiếng (UTC+7) bằng cách tạo một date mới
    date.setHours(date.getHours() + 7);

    // Lấy ngày tháng năm theo giờ VN
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // tháng từ 0-11
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}_${month}_${day}`;
}

/* Scroll to message in section */
scrollMsgInSection = function($section, isScrollInsideSection) {
    let $sectionScroll = $section;
    if (isScrollInsideSection !== true) $sectionScroll = $('html, body');
    $section.find($('[id^="MSG_"]')).each(function(i, obj) {
        if (!isEmpty($(obj)) && !isEmpty($(obj).text())) {
            scrollToElement($sectionScroll, $(obj), isScrollInsideSection);
            return false;
        }
    });
}

scrollSpanMsgInSection = function($section, isScrollInsideSection) {
    let $sectionScroll = $section;
    if (isScrollInsideSection !== true) $sectionScroll = $('html, body');

    // Tìm tất cả các <span> có class chứa 'error-message' và có text
    $section.find('span.error-message').each(function(i, obj) {
        if (!isEmpty($(obj)) && !isEmpty($(obj).text())) {
            scrollToElement($sectionScroll, $(obj), isScrollInsideSection);
            return false; // Dừng lại sau khi scroll tới phần tử đầu tiên
        }
    });
}

/* Scroll to element */
scrollToElement = function($section, $elementWantToScroll, isScrollInsideSection) {
    if (isEmpty($section) || isEmpty($elementWantToScroll)) return;
    if (isEmpty(isScrollInsideSection) || isScrollInsideSection !== true) {
            $section.animate({
            scrollTop: $elementWantToScroll.offset().top - 180 // Cách phần tử 180px phía trên
        }, 'smooth');
    } else {
        $section.animate({
            scrollTop: $elementWantToScroll.offset().top - $section.offset().top + $section.scrollTop()
                    - 180 // Cách phần tử 180px phía trên
        }, 'smooth');
    }
}

/* Generate random string */
function generateRandomString(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}

var showToastSuccess = function(position, message) {
    'use strict';
    resetToastPosition();
    $.toast({
    heading: 'Thông báo',
    text: message,
    position: String(position),
    icon: 'success',
    stack: false,
    loaderBg: '#f96868'
    , hideAfter: 3000
    })
}

// ALert failure
showToastFailure = function(position, message) {
    'use strict';
    resetToastPosition();
    $.toast({
    heading: 'Thông báo',
    text: message,
    position: String(position),
    icon: 'error',
    stack: false,
    loaderBg: '#f96868'
    })
}

// ALert info
var showToastInfo = function(position, message) {
    'use strict';
    resetToastPosition();
    $.toast({
    heading: 'Thông báo',
    text: message,
    position: String(position),
    icon: 'info',
    stack: false,
    loaderBg: '#f96868'
    })
}

// ALert info
var showToastWarning = function(position, message) {
    'use strict';
    resetToastPosition();
    $.toast({
    heading: 'Thông báo',
    text: message,
    position: String(position),
    icon: 'warning',
    stack: false,
    loaderBg: '#f96868'
    })
}

function setErrorMsg(request, prefix) {
    var errors = request.responseJSON != null ? request.responseJSON.ERRORS : null;
    // Looping các key của error messages
    for (let key in errors) {
        if(errors.hasOwnProperty(key)){
            // Lopping danh sách lỗi
            let keyVals = errors[key];
            let errorMsg = '';
            for(var i in keyVals) {
                let keyVal = keyVals[i];
                errorMsg += keyVal;
                if (i < keyVals.length - 1) errorMsg += ' ';
            }
            // Set error message
            let msg = isEmpty(prefix) ? '#MSG_' : '#MSG_' + prefix + '_';
            $(msg + key.replaceAll('.', '\\.')).text(errorMsg);
            }
    }
}

resetAllErrorMsg = function(containerId = null) {
    if (isEmpty(containerId)) {
        $('[id^="MSG_"]').each(function(i, obj) {
            $(this).text('');
        });
    } else {
        $('#' + containerId).find('[id^="MSG_"]').each(function(i, obj) {
            $(this).text('');
        });
    }
    
}

convertNewlinesToBr = function(value) {
    if (isEmpty(value)) return value;

    return value.replace(/\n/g, '<br>');
}

function redirectErrorPage(typeError) {
    let uriErrorPage;
    switch(typeError) {
        case 404:
            uriErrorPage = '{{ url("/admin/error/page-not-found") }}';
            break;
        case 500:
            break;
        default:
            break;
    }
    window.location.href = uriErrorPage;
}

/**
 * Popup confirm warning popup
 *
 * @param 
 */
/* START swal2 popup */
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-info",
        cancelButton: "btn btn-danger"
    },
    buttonsStyling: false
});
function showSwalWarningPopup(callback, message) {
    swalWithBootstrapButtons.fire({
        title: message ? message : 'Bạn có muốn xóa không?',
        // text: "You won't be able to revert this!",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Có",
        cancelButtonText: "Không",
        reverseButtons: true
    }).then((result) => {
        callback(result);
        /* if (result.isConfirmed) {
            swalWithBootstrapButtons.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
            });
        } else if (
            // Read more about handling dismissals below
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Cancelled",
            text: "Your imaginary file is safe :)",
            icon: "error"
            });
        } */
    });
}
/* END swal2 popup */

function showSwalInfoPopup(callback, message) {
    // Sử dụng SweetAlert2 với styling mặc định thay vì Bootstrap
    Swal.fire({
        title: message ? message : 'Bạn có đóng không?',
        text: "",
        icon: "info",
        showCancelButton: false,
        confirmButtonText: "Đóng",
        confirmButtonColor: "#3085d6",
        reverseButtons: true,
        // Ngăn chặn xê dịch layout
        didOpen: () => {
            // Ngăn SweetAlert2 thêm padding vào body
            document.body.style.paddingRight = '0px !important';
        },
        willClose: () => {
            // Khôi phục trạng thái ban đầu
            document.body.style.paddingRight = '';
        }
    }).then((result) => {
        callback(result);
    });
}
/* END swal2 popup */

function updUrlWithoutReloadPage(url) {
    window.history.pushState(null, null, url);
}


/* START kiểm tra device là mobile hay destop - Và trình duyệt browser nào */
function isMobileDevice() {
    return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
}

function getBrowserName() {
    let userAgent = navigator.userAgent;

    if (userAgent.indexOf("Edg") > -1) {
        return "Microsoft Edge";
    } else if (userAgent.indexOf("Chrome") > -1) {
        return "Google Chrome";
    } else if (userAgent.indexOf("Safari") > -1 && userAgent.indexOf("Chrome") === -1) {
        return "Safari";
    } else if (userAgent.indexOf("Firefox") > -1) {
        return "Mozilla Firefox";
    } else if (userAgent.indexOf("Opera") > -1 || userAgent.indexOf("OPR") > -1) {
        return "Opera";
    } else if (userAgent.indexOf("Trident") > -1) {
        return "Internet Explorer";
    } else {
        return "Unknown Browser";
    }
}

function checkDeviceAndBrowser() {
    let deviceType = isMobileDevice() ? "Mobile" : "Desktop";
    let browserName = getBrowserName();
    
}
checkDeviceAndBrowser();
/* END kiểm tra device là mobile hay desktop - Và trình duyệt browser nào */

// Select2 Ngăn bàn phím mở tự động khi dropdown được mở
function preventOpenKeyboardSelect2() {
    if (/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        const intrvalChckSearch = setInterval(() => {
            let searchField = document.querySelector('.select2-search__field');
            if (searchField) {
                searchField.blur(); // Ngăn bàn phím mở tự động khi dropdown được mở
                // Thoát repeat interval
                clearInterval(intrvalChckSearch);
            }
        }, 0.5); // Lặp lại repeat kiểm tra mỗi 0.5ms

        // Thoát kiểm tra tồn tại search trong select2 cho phép chờ tối đa 5s
        setTimeout(() => {
            // Thoát repeat interval
            clearInterval(intrvalChckSearch);
        }, 5000);
    }
}

// Select2 Khi click vào trường tìm kiếm, focus lại để mở bàn phím
$(document).on('click', '.select2-search__field', function() {
    if (/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        this.focus(); // Focus lại để kích hoạt bàn phím ảo
    }
});

// Select2 enable disable
function enableDisableSelect2($element, isEnable) {
    $element.prop('disabled', !isEnable);
}

// Select2 Set value cho select2
function setValueSelect2($select2, value) {
    $select2.val(value).trigger('change');
}


/* START function của tinyMCE */
// Hàm đọc file thành Base64 (Promise)
function readFileAsDataURLTinyMCE(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
        reader.readAsDataURL(file);
    });
}

// Hàm tải ảnh từ DataURL (Promise)
function loadImageTinyMCE(dataURL) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = (error) => reject(error);
        img.src = dataURL;
    });
}

// Hàm resize ảnh và trả về Base64
function resizeImageTinyMCE(img, type, maxWidth, maxHeight) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    let width = img.width;
    let height = img.height;

    if (width > height) {
        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }
    } else {
        if (height > maxHeight) {
            width = Math.round((width * maxHeight) / height);
            height = maxHeight;
        }
    }

    canvas.width = width;
    canvas.height = height;
    ctx.drawImage(img, 0, 0, width, height);

    return canvas.toDataURL(type, 0.9); // Nén chất lượng 90%
}

// Hàm chuyển Base64 thành Blob
function dataURLtoBlobTinyMCE(dataURL) {
    const arr = dataURL.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], { type: mime });
}
/* END function của tinyMCE */

/* Fornat số tiền VNĐ */
function formatVND(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}


/* Format tiền VNĐ sang chữ */
function formatVNDToText(amount) {
    if (typeof amount !== 'number' || isNaN(amount)) return "Số không hợp lệ";

    const units = ["đồng", "nghìn", "triệu", "tỷ", "nghìn tỷ", "triệu tỷ"];
    let text = "";
    let unitIndex = 0;

    while (amount > 0) {
        const chunk = amount % 1000; // Lấy nhóm 3 chữ số cuối
        if (chunk > 0) {
            const chunkText = chunk.toLocaleString('vi-VN'); // Định dạng số có dấu chấm
            text = `${chunkText} ${units[unitIndex]} ${text}`.trim();
        }
        amount = Math.floor(amount / 1000); // Bỏ 3 chữ số cuối
        unitIndex++;
    }

    return text.trim();
}

/* Chỉnh độ cao height lớn nhất - apply tất cả swiper của container cho đồng bộ */
function setSlidesAutoHeight(selector) {
    // Lấy các phần tử slide từ container đang chứa Swiper
    const slides = document.querySelectorAll(`${selector} .swiper-slide`);
    let maxHeight = 0;

    // Đặt chiều cao tự động trước khi tính chiều cao thực tế
    slides.forEach(slide => {
        slide.style.height = 'auto';
    });

    // Tìm chiều cao lớn nhất
    slides.forEach(slide => {
        const slideHeight = slide.offsetHeight;
        if (slideHeight > maxHeight) {
        maxHeight = slideHeight;
        }
    });

    // Đặt chiều cao lớn nhất cho tất cả slides
    slides.forEach(slide => {
        slide.style.height = `${maxHeight}px`;
    });
}
