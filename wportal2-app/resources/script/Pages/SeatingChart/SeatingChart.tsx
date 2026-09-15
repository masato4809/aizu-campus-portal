import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { SeatingChartCanvas } from '@/script/Pages/SeatingChart/SeatingChartCanvas';
import { responsiveSpacing } from '@/script/System/Responsive';
import { useIndexContext } from '@/script/Pages/SeatingChart/Index';

interface IProps extends IPropsBase {}
export const SeatingChart: React.FC<IProps> = () => {
  const { trnSeatList } = useIndexContext();

  return (
    <Page>
      <TypoH1>{getPagesName(E_PAGES.SEATING_CHART)}</TypoH1>
      <SeatingChartCanvas
        sx={{ marginTop: responsiveSpacing(4) }}
        trnSeatList={trnSeatList}
      />
    </Page>
  );
};
