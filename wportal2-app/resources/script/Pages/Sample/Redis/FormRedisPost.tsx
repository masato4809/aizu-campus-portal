import * as React from 'react';
import { Box } from '@mui/material';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { IPropsBase } from '@/script/System/System';
import { IFormRedis } from '@/script/Pages/Sample/Redis/FormRedis';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';

interface IProps extends IPropsBase {
  loading: boolean;
  formRedis: IFormRedis;
}
export const FormRedisPost: React.FC<IProps> = ({ sx, loading, formRedis }) => {
  const { csrfToken } = useCommonIndexContext();

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <form
        action={getPagesHref(E_PAGES.SAMPLE_REDIS_POST_RESULT)}
        method="POST"
      >
        <input type="hidden" name="store_value" value={formRedis.storeValue} />
        <input type="hidden" name="_token" value={csrfToken} />
        <ButtonGeneral
          sx={{
            marginTop: '10px',
          }}
          type="submit"
          buttonType={E_BUTTON_TYPE.CONTAINED_PRIMARY}
          label="直接のPost通信で保存"
          disabled={loading}
        />
      </form>
    </Box>
  );
};
