<section class="reports-page">
    <div class="reports-container">
        <div class="reports-header">
            <h1>Отчёты</h1>
            <p>Формирование и выгрузка отчётной информации по данным интерактивной карты Кузбасса.</p>
        </div>

        <div class="reports-list">
            <?php foreach ($reports as $report): ?>
                <section class="report-card">
                    <div class="report-card-top">
                        <div>
                            <h2><?= htmlspecialchars($report['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <p class="report-description"><?= htmlspecialchars($report['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>

                        <div class="report-actions">
                            <a class="report-action-button" href="?page=reports/export&type=<?= urlencode($report['type']) ?>&format=xlsx">Скачать Excel</a>
                            <a class="report-action-button" href="?page=reports/export&type=<?= urlencode($report['type']) ?>&format=docx">Скачать Word</a>
                        </div>
                    </div>

                    <?php if ($report['rows'] === []): ?>
                        <div class="report-empty-state">Данные для отчёта отсутствуют</div>
                    <?php else: ?>
                        <div class="report-table-wrap">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <?php foreach ($report['columns'] as $label): ?>
                                            <th><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report['rows'] as $row): ?>
                                        <tr>
                                            <?php foreach (array_keys($report['columns']) as $key): ?>
                                                <td><?= htmlspecialchars((string) ($row[$key] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
