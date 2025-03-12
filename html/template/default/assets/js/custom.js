$(function() {
    class StockChecker {
        constructor() {
            this.initializeEventHandlers();
        }

        initializeEventHandlers() {
            $('#quantity').on('change', (e) => this.handleQuantityChange($(e.target)));
        }

        handleQuantityChange($input) {
            const $form = $input.closest('form');
            
            // 規格設定がある商品において規格がまだ選択されていない時は、在庫チェックを行わない
            if (!this.isClassCategorySelected($form)) {
                return;
            }

            const stockInfo = this.getStockInfo($form);
            this.updateStockMessage($input, stockInfo);
        }

        isClassCategorySelected($form) {
            const classcatId1 = this.getClassCategoryId($form, 'classcategory_id1');
            const classcatId2 = this.getClassCategoryId($form, 'classcategory_id2');
            return !(classcatId1 === '__unselected' || classcatId2 === '__unselected');
        }

        getClassCategoryId($form, name) {
            return $form.find(`select[name="${name}"]`).val() || '';
        }

        getStockInfo($form) {
            const classcatId1 = this.getClassCategoryId($form, 'classcategory_id1');
            const classcatId2 = this.getClassCategoryId($form, 'classcategory_id2');
            
            return {
                available: this.getAvailableStock(classcatId1, classcatId2),
                current: this.getCurrentQuantity($('#quantity'))
            };
        }

        getAvailableStock(classcatId1, classcatId2) {
            const classcat1 = eccube.classCategories[classcatId1];
            if (classcat1) {
                const classcat2 = classcat1['#' + classcatId2];
                if (classcat2) {
                    return classcat2.stock;
                }
            } else {
                return eccube.classCategories['__unselected2']['#'].stock;
            }
            return 0;
        }

        getCurrentQuantity($input) {
            const quantity = parseInt($input.val(), 10);
            return isNaN(quantity) ? 0 : quantity;
        }

        updateStockMessage($input, stockInfo) {
            let $message = $('#stock-check-message');
            if ($message.length === 0) {
                $input.after('<div id="stock-check-message" style="color: red; margin-top: 5px;"></div>');
                $message = $('#stock-check-message');
            }

            $message.text(stockInfo.current > stockInfo.available
                ? `在庫が不足しています。利用可能な在庫: ${stockInfo.available}個`
                : '在庫が十分にあります。');
        }
    }

    new StockChecker();
});
