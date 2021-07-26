                <tr>
                  <td>
                    <?= $this->esc($payment['paid_at_formatted']) ?>
                  </td>
                  <td>
                    <?= $numberFormatter->formatCurrency($payment['display_amount'], $payment['currency_code']) ?>
                  </td>
                  <td>
                    <?= $this->esc($payment['payment_type_name']) ?>
                  </td>
                </tr>
